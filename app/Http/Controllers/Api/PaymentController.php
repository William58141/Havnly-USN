<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Api\JsonException;
use App\Http\Controllers\Controller;
use App\Support\Facades\Neonomics;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    private $userId;
    private $bankId;
    private $personalNumber;
    private $body;

    public function __construct(Request $request)
    {
        $this->userId = $request->header('x-user-id');
        $this->bankId = $request->header('x-bank-id', '');
        $this->personalNumber = $request->header('x-identification-id', '');
        $this->body = json_decode($request->getContent());

        if (!$this->userId) {
            throw new JsonException(400, 'x-user-id is required.');
        }
    }

    public function newPayment(Request $request)
    {
        if (!$this->bankId) throw new JsonException(400, 'x-bank-id is required.');
        if (!$this->personalNumber) throw new JsonException(400, 'x-identification-id is required.');
        $request->validate([
            'debtor_account' => ['required', 'array'],
            'debtor_account.owner' => ['required', 'string'],
            'debtor_account.bban' => ['required', 'string'],
            'creditor_account' => ['required', 'array'],
            'creditor_account.owner' => ['required', 'string'],
            'creditor_account.bban' => ['required', 'string'],
            'currency' => ['required', 'string'],
            'amount' => ['required', 'string'],
            'identification' => ['required', 'string'],
            'note' => ['string'],
        ]);
        $payment = Neonomics::newPayment($this->userId, $this->bankId, $this->personalNumber, $this->body);
        return $this->responseJson($payment);
    }
}
