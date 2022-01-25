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
        // return $this->request->test($value);
        return 'get banks';
    }

    public function getBankByID(string $token, string $id)
    {
        return 'bank by ID';
    }

    // public function getBanks
}
