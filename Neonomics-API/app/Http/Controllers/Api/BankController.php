<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\Facades\Neonomics;

class BankController extends Controller
{
    public function __construct()
    {
        //
    }

    // get list of all available banks
    public function index()
    {
        $banks = Neonomics::getBanks('token');

        return response()->json([
            'ok' => true,
            'result' => $banks,
        ]);
    }

    public function show($id)
    {
        return 'banks by ' . $id;
    }
}
