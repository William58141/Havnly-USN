<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\Facades\Neonomics;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $params = $this->getQueryParams($request);
        $res = Neonomics::getBanks($user->access_token, $params);
        return $this->responseJson($res);
    }

    public function show($id)
    {
        $user = auth()->user();
        $res = Neonomics::getBankByID($user->access_token, $id);
        return $this->responseJson($res);
    }

    // HELPER METHODS

    private function getQueryParams(Request $request)
    {
        $params = '';
        if ($request->has('name')) {
            $params = 'name=' . $request->query('name');
        } else if ($request->has('countryCode')) {
            $params = 'countryCode=' . $request->query('countryCode');
        }
        return $params;
    }
}
