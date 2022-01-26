<?php

namespace App\Support;

class Neonomics
{
    private $client;
    private $data;

    public function __construct(Request $client)
    {
        $this->client = $client;

        // ignore unique device-id for banks
        $this->data['headers']['x-device-id'] = 'neonomics';
    }

    //------//
    // AUTH //
    //------//

    // ...

    //-------//
    // BANKS //
    //-------//

    public function getBanks(string $token, $request)
    {
        $filter = $request->has('countryCode') ? 'countryCode=' . $request->query('countryCode') : ($request->has('name') ? 'name=' . $request->query('name') : (false));
        $uri = 'banks';
        if ($filter) {
            $uri = $uri . '?' . $filter;
        }
        $response = $this->client->request($token, 'get', $uri, $this->data);
        return $response;
    }

    public function getBankByID(string $token, string $id)
    {
        $response = $this->client->request($token, 'get', "banks/{$id}", $this->data);
        return $response;
    }

    //---------//
    // ACCOUNT //
    //---------//

    public function getAccounts(string $token)
    {
        // $data['headers']['x-device-id'] = $session->deviceId;
        // $data['headers']['x-session-id'] = $session->sessionId;
        // $response = $this->client->request($token, 'get', 'accounts', $data);
        // return $response;
        return 'not done';
    }

    //---------//
    // PAYMENT //
    //---------//

}
