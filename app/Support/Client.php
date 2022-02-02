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

    public function getTokens(string $clientId, string $clientSecret)
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
    }

    public function refreshTokens(string $clientId, string $clientSecret, string $refreshToken)
    {
        $data = [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
            ]
        ];
        $res = $this->apiHelper->request('', 'POST', env('NEONOMICS_AUTH_URL'), $data);
        return $res;
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
