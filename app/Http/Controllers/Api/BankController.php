<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\Facades\Neonomics;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function index(Request $request)
    {
        $token = auth()->user()->access_token;
        $params = $request->has('countryCode') ? 'countryCode=' . $request->query('countryCode') : ($request->has('name') ? 'name=' . $request->query('name') : (false));
        $data = Neonomics::getBanks($token, $params);
        return $this->responseJson($data);
    }

    public function show($id)
    {
        $token = auth()->user()->access_token;
        $data = Neonomics::getBankByID($token, $id);
        return $this->responseJson($data);
    }
}
