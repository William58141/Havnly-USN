<?php

namespace App\Support;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Request
{
    private $http;

    public function __construct(Client $http)
    {
        $this->http = $http;
    }

    public function send(string $token, string $method, string $uri, array $data = [])
    {
        if (!array_key_exists('headers', $data)) {
            $data['headers'] = [
                'authorization' => "Bearer {$token}",
                'x-device-id' => 'neonomics',
                'accept' => 'application/json',
            ];
        }

        try {
            $response = $this->http->request($method, $uri, $data);
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
