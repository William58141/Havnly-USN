<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Api\JsonException;
use App\Http\Controllers\Controller;
use App\Support\Facades\Neonomics;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    private $userId;
    private $bankId;
    private $personalNumber;

    public function __construct(Request $request)
    {
        $this->userId = $request->header('x-user-id');
        $this->bankId = $request->header('x-bank-id');
        $this->personalNumber = $request->header('x-identification-id', '');

        if (!$this->userId || !$this->bankId) {
            throw new JsonException(400, 'x-user-id and x-bank-id is required.');
        }
    }

    public function index()
    {
        $res = Neonomics::getAccounts($this->userId, $this->bankId, $this->personalNumber);
        return $this->responseJson($res);
    }

    public function show(string $id)
    {
        $res = Neonomics::getAccountByID($this->userId, $this->bankId, $this->personalNumber, $id);
        return $this->responseJson($res);
    }

    public function showBalances(string $id)
    {
        $res = Neonomics::getAccountBalancesByID($this->userId, $this->bankId, $this->personalNumber, $id);
        return $this->responseJson($res);
    }

    public function showTransactions(string $id)
    {
        $res = Neonomics::getAccountTransactionsByID($this->userId, $this->bankId, $this->personalNumber, $id);
        return $this->responseJson($res);
    }
}
