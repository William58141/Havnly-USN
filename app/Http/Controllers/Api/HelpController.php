<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class HelpController extends Controller
{
    public function index()
    {
        return response()->json([
            'ok' => true,
            'result' => [
                [
                    'uri' => '/auth',
                    'description' => 'Used to authenticate and get access token',
                ],
                [
                    'uri' => '/banks',
                    'description' => 'Get a list of all available banks',
                ],
                [
                    'uri' => '/banks/{id}',
                    'description' => 'Get a specific bank based on its ID',
                ],
                [
                    'uri' => '/banks?countryCode={value}',
                    'description' => 'Get a list of banks within a given country',
                ],
                [
                    'uri' => '/banks?name={value}',
                    'description' => 'Get a specific bank based on its name',
                ],
                [
                    'uri' => '/help',
                    'description' => 'Shows this result',
                ],
            ],
        ]);
    }
}
