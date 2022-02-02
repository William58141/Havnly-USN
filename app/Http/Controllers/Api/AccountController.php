<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\Facades\Neonomics;

class AccountController extends Controller
{
    // get list of all available banks
    // or check for query filter
    public function index()
    {
        $token = 'SOME VALUE';
        return Neonomics::getAccounts($token);
    }

    public function show($id)
    {
        // $token = 'SOME VALUE';
        // return Neonomics::getAccountById($token, $id);
        return 'not done';
    }

    public function balances($id)
    {
        return 'not done';
    }

    public function transactions($id)
    {
        return 'not done';
    }
}
