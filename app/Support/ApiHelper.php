<?php

namespace App\Support;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\GuzzleException;
use SebastianBergmann\Environment\Console;

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
     * @return array [$status, $body]
     */
    public function request(string $token, string $method, string $uri, array $data = [])
    {
        $data['headers']['authorization'] = "Bearer {$token}";
        $data['headers']['accept'] = 'application/json';

        try {
            $response = $this->httpClient->request($method, $uri, $data);
            return $this->response($response->getStatusCode(), json_decode($response->getBody()));
        } catch (RequestException $e) {
            $errorCode = $e->getResponse()->getStatusCode();
            if ($errorCode == 510) {
                return $this->clientError($e);
            } else if ($errorCode == 520) {
                return $this->neonomicsError($e);
            } else if ($errorCode == 530) {
                return $this->bankError($e);
            }
            return $this->errorResponse(502, 'Bad Gateway.');
        } catch (GuzzleException $e) {
            return $this->errorResponse(500, 'Internal Server Error.');
        }
    }

    // -------------- //
    // Error handlers //
    // -------------- //

    private function clientError($e)
    {
        if ($e->hasResponse()){
            $body = json_decode($e->getResponse()->getBody());
            $errorCode = property_exists($body, 'errorCode') ? $body->errorCode : null;
            $message = property_exists($body, 'message') ? $body->message : null;

            if ($errorCode < 2000) {
                // bad request
                return $this->errorResponse(400, 'Bad Request.', $message);
            } else {
                // auth
                return $this->errorResponse(401, 'Unauthorized.', $message);
            }
        }
    }

    private function neonomicsError($e)
    {
        return 'neonomics error';
    }

    private function bankError($e)
    {
        return 'bank error';
    }

    // private fnction errorHandler($e)
    // {
    //     if ($e->hasResponse()) {
    //         $res = $e->getResponse();
    //         return response()->json([
    //             'ok' => false,
    //             'error' => $res->getStatusCode(),
    //             'result' => $res->getBody(),
    //         ], $res->getStatusCode());
    //     }
    //     return response()->json([
    //         'ok' => false,
    //         'error' => 502,
    //         'description' => 'Bad Gateway',
    //     ], 502);
    // }

    // ----------- //
    // Request fix //
    // ----------- //

    private function authenticate()
    {
        // get auth url
    }

    private function consent()
    {
        // get consent url
    }

    // --------------- //
    // Response format //
    // --------------- //

    private function response(int $status, $body)
    {
        return [
            'ok' => true,
            'status' => $status,
            'body' => $body,
        ];
    }

    private function errorResponse(int $status, string $description, string $message = null)
    {
        $data = [
            'ok' => false,
            'error' => $status,
            'description' => $description,
        ];
        if ($message) {
            $data['message'] = $message;
        }
        return [
            'ok' => false,
            'status' => $status,
            'body' => $data,
        ];
    }
}
