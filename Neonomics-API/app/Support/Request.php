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

    public function test($value)
    {
        return $value . ' ' . $value . 'request';
    }
}
