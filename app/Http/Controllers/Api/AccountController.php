<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Api\JsonException;
use App\Http\Controllers\Controller;
use App\Support\Facades\Neonomics;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    private $status = 200;
    private $userId;
    private $bankId;
    private $personalNumber;

    public function __construct(Request $request)
    {
        $this->userId = $request->header('x-user-id');
        $this->bankId = $request->header('x-bank-id');
        $this->personalNumber = $request->header('x-identification-id', '');

        if (!$this->userId || !$this->bankId) {
            throw new JsonException(400, 'x-user-id and x-bank-id is required');
        }
    }

    public function index()
    {
        $accounts = Neonomics::getAccounts($this->userId, $this->bankId, $this->personalNumber);
        $this->isConsentMissing($accounts);
        return $this->responseJson($accounts, $this->status);
    }

    public function show($id)
    {
        $accounts = Neonomics::getAccountByID($this->userId, $this->bankId, $this->personalNumber, $id);
        $this->isConsentMissing($accounts);
        return $this->responseJson($accounts, $this->status);
    }

    public function showBalances($id)
    {
        $accounts = Neonomics::getAccountBalancesByID($this->userId, $this->bankId, $this->personalNumber, $id);
        $this->isConsentMissing($accounts);
        return $this->responseJson($accounts, $this->status);
    }

    public function showTransactions($id)
    {
        $accounts = Neonomics::getAccountTransactionsByID($this->userId, $this->bankId, $this->personalNumber, $id);
        $this->isConsentMissing($accounts);
        return $this->responseJson($accounts, $this->status);
    }

    // HELPER METHODS

    private function isConsentMissing($res)
    {
        if (is_array($res) && array_key_exists('href', $res)) {
            $this->status = 409;
        }
    }
}
