<?php

namespace App\Support;

use App\Exceptions\Api\JsonException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\GuzzleException;

class ApiHelper
{
    private $httpClient;

    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
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
        $data['headers']['accept'] = 'application/json';

        try {
            $response = $this->httpClient->request($method, $uri, $data);
            return json_decode($response->getBody());
        } catch (RequestException $e) {
            $errorCode = $e->getResponse()->getStatusCode();
            if ($errorCode == 510) {
                return $this->clientError($e);
            } else if ($errorCode == 520) {
                return $this->neonomicsError($e);
            } else if ($errorCode == 530) {
                return $this->bankError($e);
            }

            throw new JsonException(502);
        } catch (GuzzleException $e) {
            throw new JsonException(500);
        }
    }

    // -------------- //
    // Error handlers //
    // -------------- //

    private function clientError($e)
    {
        if ($e->hasResponse()) {
            // [$errorCode, $message] = $this->getErrorData($e);
            $body = $this->getErrorBody($e);

            // consent
            if ($body->errorCode === "1426") {
                // $this->initConsent($body->links);
                throw new JsonException(400, 'consent neede');
            }
            // authorize payment
            if ($body->errorCode === "1428") {
                //
            }
            // bad request
            if ($body->errorCode < 2000) {
                throw new JsonException(400, $body->message);
            }
            // forbidden
            if ($body->errorCode === "2004") {
                throw new JsonException(403, $body->message);
            }
            // invalid client id/secret
            if ($body->errorCode === "2005") {
                throw new JsonException(401, $body->message);
            }
            // $this->reAuthenticate();
            throw new JsonException(500, $e);
        }
        // unknown
        throw new JsonException(410, 'We do not know what happened');
    }

    private function neonomicsError($e)
    {
        throw new JsonException(500, 'Error from Neonomics');
    }

    private function bankError($e)
    {
        throw new JsonException(500, 'Error from bank');
    }

    /**
     * Get status and message from response
     *
     * @param RequestException $e
     * @return object
     */
    private function getErrorBody($e)
    {
        $body = json_decode($e->getResponse()->getBody());

        if (!is_object($body)) throw new JsonException(400);

        // $errorCode = property_exists($body, 'errorCode') ? $body->errorCode : null;
        // $message = property_exists($body, 'message') ? $body->message : null;

        // return [$errorCode, $message];
        return $body;
    }
}
