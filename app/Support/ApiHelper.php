<?php

namespace App\Support;

use App\Exceptions\Api\ConsentRequiredException;
use App\Exceptions\Api\JsonException;
use App\Support\Facades\Neonomics;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\GuzzleException;

class ApiHelper
{
    private $httpClient;
    private $lastFailedRequest;

    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    private function runLastFailedRequest(string $token = '')
    {
        return $this->request($token, ...$this->lastFailedRequest);
    }

    /**
     * Send a new http request.
     *
     * @param string $token
     * @param string $method
     * @param string $uri
     * @param array $data
     * @return object
     */
    public function request(string $token, string $method, string $uri, array $data = [])
    {
        if ($token) $data['headers']['authorization'] = "Bearer {$token}";

        try {
            $response = $this->httpClient->request($method, $uri, $data);
            return json_decode($response->getBody());
        } catch (RequestException $e) {
            $this->lastFailedRequest = [$method, $uri, $data];
            $errorCode = $e->getResponse()->getStatusCode();

            if ($errorCode == 510) {
                return $this->clientError($e);
            }
            if ($errorCode == 520) {
                return $this->neonomicsError($e);
            }
            if ($errorCode == 530) {
                return $this->bankError($e);
            }

            throw new JsonException(502);
        } catch (GuzzleException $e) {
            throw new JsonException(500);
        }
    }

    private function clientError($e)
    {
        $body = $this->getErrorBody($e);

        // invalid payment id state
        if ($body->errorCode === "1009") {
            throw new JsonException(409, 'Resource ID is in invalid state, payment already completed.');
        }
        // consent or payment authorization
        if ($body->errorCode === "1426" || $body->errorCode === "1428") {
            throw new ConsentRequiredException($body);
        }
        // bad request
        if ($body->errorCode < 2000) {
            throw new JsonException(400, $body->message);
        }
        // invalid or expired access token
        if ($body->errorCode === "2001" || $body->errorCode === "2002") {
            $useRefreshToken = true;
            $res = Neonomics::updateTokens($useRefreshToken);
            return $this->runLastFailedRequest($res->access_token);
        }
        // forbidden
        if ($body->errorCode === "2004") {
            throw new JsonException(403, $body->message);
        }
        // invalid client id/secret
        if ($body->errorCode === "2005") {
            throw new JsonException(401, $body->message);
        }
        // expired refresh token
        if ($body->errorCode === "2009") {
            $res = Neonomics::updateTokens();
            return $this->runLastFailedRequest($res->access_token);
        }
        // generic
        throw new JsonException(400, $body->message);
    }

    private function neonomicsError($e)
    {
        $body = $this->getErrorBody($e);

        // network error
        if ($body->errorCode === "3901") {
            throw new JsonException(408, 'Network error, please retry');
        }
        // session or consent missing
        if ($body->errorCode === "3010" || $body->errorCode === "3011") {
            // ! unsure of best way to handle this error
            throw new JsonException(409, 'Session or consent missing, please retry.');
        }
        // default
        throw new JsonException(503, "Error-{$body->errorCode} from Neonomics, please contact us.");
    }

    private function bankError($e)
    {
        $body = $this->getErrorBody($e);

        // x-psu-id is required by the bank
        if ($body->errorCode === "5001") {
            throw new JsonException(400, 'x-identification-id is required.');
        }
        throw new JsonException(503, "Error-{$body->errorCode} from selected bank, please contact us.");
    }

    private function getErrorBody($e)
    {
        $body = json_decode($e->getResponse()->getBody());
        if (!is_object($body)) throw new JsonException(400);

        // catch unknown
        if (!property_exists($body, 'errorCode')) {
            if (property_exists($body, 'message')) {
                throw new JsonException(503, $body->message);
            }
            throw new JsonException(503, 'Unknown error, please contact us.');
        }

        return $body;
    }
}
