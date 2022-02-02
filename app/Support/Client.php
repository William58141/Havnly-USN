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

    // ...

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
        $data = $this->apiHelper->request($token, 'GET', $uri, $data);
        $data['body'] = $data['ok'] ? Bank::jsonDeserialize($data['body']) : $data['body'];
        return $data;
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
        $data = $this->apiHelper->request($token, 'GET', "banks/{$id}", $data);
        $data['body'] = $data['ok'] ? Bank::jsonDeserialize($data['body']) : $data['body'];
        return $data;
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
