<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Api\JsonException;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Support\Facades\Neonomics;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function auth(Request $request)
    {
        $status = 200;
        $request->validate([
            'name' => 'required|string',
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
            'encryption_key' => 'required|string',
            'redirect_url' => 'required|string',
        ]);
        $user = User::where('client_id', $request->client_id)->first();

        if (!$user) {
            $status = 201;
            $user = $this->createUser($request);
        } else {
            $this->authenticate($user, $request);
            $this->updateUser($user, $request);
        }

        $user->tokens()->delete();
        $authToken = $user->createToken($user->name)->plainTextToken;

        return $this->responseJson(['access_token' => $authToken], $status);
    }

    private function createUser(Request $request)
    {
        $tokens = Neonomics::getTokens($request->client_id, $request->client_secret);
        $user = User::create([
            'name' => $request->name,
            'client_id' => $request->client_id,
            'client_secret' => $request->client_secret,
            'encryption_key' => $request->encryption_key,
            'redirect_url' => $request->redirect_url,
            'access_token' => $tokens->access_token,
            'refresh_token' => $tokens->refresh_token,
        ]);

        return $user;
    }

    private function authenticate(User $user, Request $request)
    {
        if ($user->client_secret !== $request->client_secret || $user->encryption_key !== $request->encryption_key) {
            throw new JsonException(401, 'Invalid client_id, client_secret or encryption_key');
        }
    }

    private function updateUser(User $user, Request $request)
    {
        if ($user->name !== $request->name) {
            $user->name = $request->name;
        }
        if ($user->redirect_url !== $request->redirect_url) {
            $user->redirect_url = $request->redirect_url;
        }
        return $user->save();
    }
}
