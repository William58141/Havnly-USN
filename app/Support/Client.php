<?php

namespace App\Support;

use App\Models\Bank;

class Client
{
    private $apiHelper;

    public function __construct(ApiHelper $apiHelper)
    {
        $this->apiHelper = $apiHelper;
    }

    //------//
    // Auth //
    //------//

    public function getTokens(string $clientId, $clientSecret)
    {
        $data = [
            'form_params' => [
                'grant_type' => 'client_credentials',
                'scope' => 'openid',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
            ]
        ];
        $res = $this->apiHelper->request('', 'POST', env('NEONOMICS_AUTH_URL'), $data);
        return $res;
        // return [
        //     'access_token' => $res->access_token,
        //     'refresh_token' => $res->refresh_token,
        // ];
    }

    private function refreshTokens()
    {
        $data = [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => 'eyJhbGciOiJIUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICIwYTAzNjlhNy0yMTg3LTQ3OWYtOTU4NS02Y2QzYTczZDUxOWYifQ.eyJleHAiOjE2NDYzMTUxNjYsImlhdCI6MTY0MzcyMzE2NiwianRpIjoiODhhYmQwYjktNjA1OS00MGYyLWI2NmQtODZmZDE1YjZhZjRmIiwiaXNzIjoiaHR0cHM6Ly9zYW5kYm94Lm5lb25vbWljcy5pby9hdXRoL3JlYWxtcy9zYW5kYm94IiwiYXVkIjoiaHR0cHM6Ly9zYW5kYm94Lm5lb25vbWljcy5pby9hdXRoL3JlYWxtcy9zYW5kYm94Iiwic3ViIjoiZmQ5MDRiMTctM2Y4Ni00NDFhLTkwODEtNmI0MmVhNDU5NjQ5IiwidHlwIjoiUmVmcmVzaCIsImF6cCI6ImYzOWFjOTVmLWY5YjgtNGNhZC05MjliLTkzMDEzNmE3OWVjNCIsInNlc3Npb25fc3RhdGUiOiIwMjllNTA1ZC01ODkzLTRiMTEtODI5Yy1hMzBmNWRhMjUzNWEiLCJzY29wZSI6Im9wZW5pZCBiYW5xYnJpZGdlX2NsaWVudCJ9.Rh1N1oHlSt0B46pReAKMOwDdfQf8xJPC4DeyrY-PD0g',
                'client_id' => 'f39ac95f-f9b8-4cad-929b-930136a79ec4',
                'client_secret' => '5fb748bb-5fe9-430d-a0ec-eb9edcc9c48a',
            ]
        ];
        return 'new access token with refresh token';
    }

    private function initConsent()
    {
        return 'init consent';
    }

    private function initPaymentAuthorization()
    {
        return 'payment auth';
    }

    //------//
    // Bank //
    //------//

    /**
     * Get a list of all banks or based on [countryCode, name]
     *
     * @param string $token
     * @param string $params
     * @return object
     */
    public function getBanks(string $token, string $params)
    {
        $uri = 'banks';
        if ($params) {
            $uri = $uri . '?' . $params;
        }
        $data['headers']['x-device-id'] = 'neonomics';
        $res = $this->apiHelper->request($token, 'GET', $uri, $data);
        $res = Bank::jsonDeserialize($res);
        return $res;
    }

    /**
     * Get bank by id
     *
     * @param string $token
     * @param string $id
     * @return object
     */
    public function getBankByID(string $token, string $id)
    {
        $data['headers']['x-device-id'] = 'neonomics';
        $res = $this->apiHelper->request($token, 'GET', "banks/{$id}", $data);
        $res = Bank::jsonDeserialize($res);
        return $res;
    }

    //---------//
    // Account //
    //---------//

    // ...

    //---------//
    // Payment //
    //---------//

    // ...

    //--------//
    // Helper //
    //--------//

    // ...
}
