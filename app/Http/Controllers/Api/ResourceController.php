<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Api\JsonException;
use App\Http\Controllers\Controller;
use App\Models\Session;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function show($id)
    {
        $session = Session::where('session_id', $id)->first();
        if (!$session) {
            throw new JsonException(400, 'Invalid resource ID');
        }
        return $this->responseJson(['userId' => $session->user_id]);
    }
}
