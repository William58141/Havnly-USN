<?php

require_once "vendor/autoload.php";

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Request
{
    private const AUTH_URL = 'https://sandbox.neonomics.io/auth/realms/sandbox/protocol/openid-connect/token';
    private const BASE_URL = 'https://sandbox.neonomics.io';

    private const CLIENT_ID = 'f39ac95f-f9b8-4cad-929b-930136a79ec4';
    private const CLIENT_SECRET = '5fb748bb-5fe9-430d-a0ec-eb9edcc9c48a';

    private $client;
    private $token;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => Request::BASE_URL,
            'headers' => [
                'authorization' => "Bearer {$this->token}",
                'content-type' => 'application/json',
            ],
        ]);
    }

    

    public function auth()
    {
        try {
            $res = $this->client->request('POST', self::AUTH_URL, [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    // 'client_id' => self::CLIENT_ID,
                    'client_id' => "ok",
                    'client_secret' => self::CLIENT_SECRET,
                ],
                'debug' => false,
            ]);
        } catch (RequestException $e) {
            echo $e->getResponse()->getStatusCode();
            echo "\t";
            // echo json_decode($e->getResponse()->getBody())->error_description;
            echo $e->getResponse()->getBody();
            echo json_decode($e->getResponse()->getBody())->message;
            echo "\n";
        }

        // echo $res->getStatusCode() . "\n\n"; // 200
        // echo $res->getReasonPhrase() . "\n\n"; // OK

        // foreach ($res->getHeaders() as $name => $values) {
        //     echo $name . ': ' . implode(',--- ', $values) . "\n";
        // }
        
        // echo $res->getHeaderLine('content-type');

        // $data = json_decode($res->getBody());
        // echo $data->access_token . "\n\n";
    }

    public function getBanks()
    {
        $res = $this->client->request('GET', '/ics/v3/banks', [
            'headers' => [
                
            ]
        ]);

        echo $res->getStatusCode() . "\n";
        echo $res->getBody() . "\n";
    }
}

$request = new Request();
$request->auth();
//$request->getBanks();
