<?php

namespace App\Support;

class Client
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getBanks(string $token)
    {
        $response = $this->request->send($token, 'get', 'banks');
        return $response;
    }

    public function getBankByID(string $token, string $id)
    {
        $response = $this->request->send($token, 'get', "banks/{$id}");
        return $response;
    }

    public function getBanksByFilter(string $token, string $filter)
    {
        $response = $this->request->send($token, 'get', "banks?{$filter}");
        return $response;
    }
}
