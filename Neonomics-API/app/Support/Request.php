<?php

namespace App\Support;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Request
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Send a new http request.
     *
     * @param string $token
     * @param string $method
     * @param string $uri
     * @param array $data
     * @return JsonResponse
     */
    public function request(string $token, string $method, string $uri, array $data = [])
    {
        // add needed headers
        $data['headers']['authorization'] = "Bearer {$token}";
        $data['headers']['accept'] = 'application/json';

        try {
            $response = $this->client->request($method, $uri, $data);
            $result = json_decode($response->getBody());
            return response()->json([
                'ok' => true,
                'result' => $result,
            ], $response->getStatusCode());
        } catch (GuzzleException $e) {
            // do something to fix...
            // use private methods

            // ! FIX - WANT TO HANDLE ALL NEONOMICS ERRORS AUTOMATICALLY
            return response()->json([
                'ok' => false,
                'error' => $e->getCode(),
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }

    private function authenticate()
    {
        // get auth url
    }

    private function consent()
    {
        // get consent url
    }
}
