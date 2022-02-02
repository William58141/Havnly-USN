<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function responseJson($data)
    {
        if ($data['ok']) {
            $response = [
                'ok' => $data['ok'],
                'result' => $data['body'],
            ];
        } else {
            $response = $data['body'];
        }
        return response()->json($response, $data['status']);
    }
}
