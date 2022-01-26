<?php

namespace App\Support;

class Client
{
    private $request;
    private $data;

    public function __construct(Request $request)
    {
        $this->request = $request;

        // ignore unique device-id for banks
        $this->data['headers']['x-device-id'] = 'neonomics';
    }

    public function getBanks(string $token, $request)
    {
        $filter = $request->has('countryCode') ? 'countryCode='.$request->query('countryCode') : ($request->has('name') ? 'name='.$request->query('name') : (false));
        $uri = 'banks';
        if ($filter) {
            $uri = $uri.'?'.$filter;
        }
        $response = $this->request->send($token, 'get', $uri, $this->data);
        return $response;
    }

    public function getBankByID(string $token, string $id)
    {
        $response = $this->request->send($token, 'get', "banks/{$id}", $this->data);
        return $response;
    }
}
