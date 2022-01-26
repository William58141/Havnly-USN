<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function index(Request $request)
    {
        $token = $request->user()->createToken($request->token_name);
        return response()->json([
            'ok' => true,
            'token' => $token->plainTextToken,
        ]);
    }
}
