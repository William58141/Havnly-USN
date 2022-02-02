<?php

namespace App\Support;

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
     * @return array [$status, $body]
     */
    public function request(string $token, string $method, string $uri, array $data = [])
    {
        $data['headers']['authorization'] = "Bearer {$token}";
        $data['headers']['accept'] = 'application/json';

        try {
            $response = $this->httpClient->request($method, $uri, $data);
            return $this->responseJson($response->getStatusCode(), json_decode($response->getBody()));
            return [
                'status' => $response->getStatusCode(),
                'body' => [
                    'ok' => true,
                    'result' => json_decode($response->getBody()),
                ]
            ];
        } catch (RequestException $e) {
            $errorCode = $e->getResponse()->getStatusCode();
            if ($errorCode == 510) {
                return $this->clientError($e);
            } else if ($errorCode == 520) {
                return $this->neonomicsError($e);
            } else if ($errorCode == 530) {
                return $this->bankError($e);
            }
            return $this->errorResponseJson(502, 'Bad Gateway');
        } catch (GuzzleException $e) {
            return $this->errorResponseJson(500, 'Internal Server Error');
        }
    }

    // -------------- //
    // Error handlers //
    // -------------- //

    private function clientError($e)
    {
        return $this->errorResponseJson(400, 'Bad Request', 'message');
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

    private function responseJson(int $status, $body)
    {
        $data = [
            'ok' => true,
            'result' => $body,
        ];
        return [
            'status' => $status,
            'body' => $data,
        ];
    }

    private function errorResponseJson(int $status, string $description, string $message = null)
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
            'status' => $status,
            'body' => $data,
        ];
    }
}
