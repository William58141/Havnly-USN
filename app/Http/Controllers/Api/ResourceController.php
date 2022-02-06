<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Api\JsonException;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Session;
use App\Support\Facades\Neonomics;

class ResourceController extends Controller
{
    public function show(string $id)
    {
        $payment = $this->getPayment($id);

        if ($payment) {
            $session = $this->getOrFailSession($payment->session_id);
            Neonomics::completePayment($id, $session->user_id, $session->session_id);
            $userId = $session->user_id;
            $action = 'Payment authorized.';
            $payment->delete();
        } else {
            $session = $this->getOrFailSession($id);
            $userId = $session->user_id;
            $action = 'Consent approved.';
        }

        return $this->responseJson([
            'user_id' => $userId,
            'action' => $action,
        ]);
    }

    private function getOrFailSession(string $id)
    {
        $session = Session::where('name', auth()->user()->name)
            ->where('session_id', $id)
            ->first();
        if (!$session) {
            throw new JsonException(400, 'Invalid resource ID.');
        }
        return $session;
    }

    private function getPayment(string $id)
    {
        return Payment::where('name', auth()->user()->name)
            ->where('payment_id', $id)
            ->first();
    }
}
