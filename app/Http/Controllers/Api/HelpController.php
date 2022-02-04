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
                'Authentication' => [
                    [
                        'uri' => '/auth',
                        'method' => 'POST',
                        'json' => [
                            'name' => 'Your application name',
                            'client_id' => 'Client_id from Neonomics',
                            'client_secret' => 'Client_secret from Neonomics',
                            'encryption_key' => 'Value of the rawValue field from the Neonomics encryption key',
                            'redirect_url' => 'Callback after user consent and payment authentication',
                        ],
                        'description' => 'Used to authenticate and get access token',
                    ],
                ],
                'Banks' => [
                    [
                        'uri' => '/banks',
                        'method' => 'GET',
                        'headers' => [
                            'authorization' => 'Bearer <token>'
                        ],
                        'description' => 'Get all available banks',
                    ],
                    [
                        'uri' => '/banks?countryCode={value}',
                        'method' => 'GET',
                        'headers' => [
                            'authorization' => 'Bearer <token>'
                        ],
                        'description' => 'Get all banks in a given country',
                    ],
                    [
                        'uri' => '/banks?name={value}',
                        'method' => 'GET',
                        'headers' => [
                            'authorization' => 'Bearer <token>'
                        ],
                        'description' => 'Get bank by it\'s name',
                    ],
                    [
                        'uri' => '/banks/{id}',
                        'method' => 'GET',
                        'headers' => [
                            'authorization' => 'Bearer <token>'
                        ],
                        'description' => 'Get bank by it\'s ID',
                    ],
                ],
                'Accounts' => [
                    [
                        'uri' => '/accounts',
                        'method' => 'GET',
                        'headers' => [
                            'authorization' => 'Bearer <token>',
                            'x-user-id' => 'Your applications user ID',
                            'x-bank-id' => 'One of the provided bank ID\'s',
                            'x-identification-id' => 'A users social security number (Required if Bank has requireIdentification=true)'
                        ],
                        'description' => 'Get all accounts for a user',
                    ],
                    [
                        'uri' => '/accounts/{id}',
                        'method' => 'GET',
                        'headers' => [
                            'authorization' => 'Bearer <token>',
                            'x-user-id' => 'Your applications user ID',
                            'x-bank-id' => 'One of the provided bank ID\'s',
                            'x-identification-id' => 'A users social security number (Required if Bank has requireIdentification=true)'
                        ],
                        'description' => 'Get account by it\'s ID',
                    ],
                    [
                        'uri' => '/accounts/{id}/transactions',
                        'method' => 'GET',
                        'headers' => [
                            'authorization' => 'Bearer <token>',
                            'x-user-id' => 'Your applications user ID',
                            'x-bank-id' => 'One of the provided bank ID\'s',
                            'x-identification-id' => 'A users social security number (Required if Bank has requireIdentification=true)'
                        ],
                        'description' => 'Get all transactions for an account',
                    ],
                    [
                        'uri' => '/accounts/{id}/balances',
                        'method' => 'GET',
                        'headers' => [
                            'authorization' => 'Bearer <token>',
                            'x-user-id' => 'Your applications user ID',
                            'x-bank-id' => 'One of the provided bank ID\'s',
                            'x-identification-id' => 'A users social security number (Required if Bank has requireIdentification=true)'
                        ],
                        'description' => 'Get all balances for an account',
                    ],
                ],
                'Other' => [
                    [
                        'uri' => '/resources/{id}',
                        'method' => 'GET',
                        'headers' => [
                            'authorization' => 'Bearer <token>'
                        ],
                        'description' => 'Get user ID related to the redirect_url resource ID',
                    ],
                    [
                        'uri' => '/help',
                        'method' => 'GET',
                        'description' => 'Shows a list of all available endpoints, with required data',
                    ],
                ],
            ],
        ]);
    }
}
