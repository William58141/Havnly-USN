<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    // public function register(Request $request)
    // {
    //     $fields = $request->validate([
    //         'clientId' => 'required|string',
    //         'clientSecret' => 'required|string',
    //     ]);

    //     $user = User::create([
    //         'clientId' => $fields['clientId'],
    //         'clientSecret' => $fields['clientSecret'],
    //     ]);

    //     $token = $user->createToken('neonomics');

    //     return response()->json([
    //         'ok' => true,
    //         'token' => $token->plainTextToken,
    //     ], 201);
    // }
}
