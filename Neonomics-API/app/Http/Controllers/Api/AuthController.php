<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\Facades\Neonomics;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private $data;
    private $data2;

    public function __construct(Request $res)
    {

        $this->data = $res;

        // $this->data = $res->route();
        // $this->data = $res->isMethod('POST');
        // $this->data = $res->isMethod('GET');
        // $this->data = $res->header();
        // $this->data2 = $res->query();
        // $this->data = $res->getQueryString();
        // $this->data = $res->getPort();
        // $this->data = $res->post();
        // $this->data = $res->all();
        // $this->data = $res->has('id');




        // $this->data = Neonomics::test('ok');
    }

    public function index()
    {
        return response()->json([
            'status' => 'OK',
            'post' => $this->data,
            'get' => $this->data2,
        ]);
    }
}
