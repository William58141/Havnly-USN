<?php

namespace App\Support;

use App\Exceptions\api\ConsentRequiredException;
use App\Exceptions\Api\JsonException;
use App\Models\Account;
use App\Models\Bank;
use App\Models\Payment;
use App\Models\Session;
use App\Models\User;
use stdClass;

class Client
{
    private $apiHelper;

    public function __construct(ApiHelper $apiHelper)
    {
        $this->apiHelper = $apiHelper;
    }

    //------//
    // Auth //
    //------//

    public function getNewTokens(string $clientId, string $clientSecret)
    {
        $data = [
            'form_params' => [
                'grant_type' => 'client_credentials',
                'scope' => 'openid',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
            ]
        ];
        return $this->apiHelper->request('', 'POST', env('NEONOMICS_AUTH_URL'), $data);
    }

    public function updateTokens(bool $useRefreshToken = false)
    {
        $user = User::where('client_id', auth()->user()->client_id)->first();
        if ($useRefreshToken) {
            $data = [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $user->refresh_token,
                    'client_id' => $user->client_id,
                    'client_secret' => $user->client_secret,
                ]
            ];
            $res = $this->apiHelper->request('', 'POST', env('NEONOMICS_AUTH_URL'), $data);
        } else {
            $res = $this->getNewTokens($user->client_id, $user->client_secret);
        }
        $user->access_token = $res->access_token;
        $user->refresh_token = $res->refresh_token;
        $user->save();
        return $res;
    }

    private function getConsent(string $url, array $data)
    {
        $user = auth()->user();
        $data['headers']['x-redirect-url'] = $user->redirect_url;
        $res = $this->apiHelper->request($user->access_token, 'GET', $url, $data);
        foreach ($res->links as $link) {
            if ($link->rel === 'consent' || $link->rel === 'Authorization URL') {
                return [
                    'type' => $link->type,
                    'rel' => $link->rel,
                    'href' => $link->href
                ];
            }
        }
        throw new JsonException(404, 'URL missing, please retry');
    }

    //------//
    // Bank //
    //------//

    public function getBanks(string $params)
    {
        $user = auth()->user();
        $uri = 'banks';
        if ($params) $uri = $uri . '?' . $params;
        $data['headers']['x-device-id'] = 'neonomics';
        $res = $this->apiHelper->request($user->access_token, 'GET', $uri, $data);
        $banks = Bank::jsonDeserialize($res);
        return $banks;
    }

    public function getBankByID(string $id)
    {
        $user = auth()->user();
        $data['headers']['x-device-id'] = 'neonomics';
        $res = $this->apiHelper->request($user->access_token, 'GET', "banks/{$id}", $data);
        $bank = Bank::jsonDeserialize($res);
        return $bank;
    }

    //---------//
    // Session //
    //---------//

    private function getOrCreateSession(string $userId, string $bankId)
    {
        $session = Session::where('name', auth()->user()->name)
            ->where('user_id', $userId)
            ->where('bank_id', $bankId)
            ->first();
        if (!$session) {
            $session = $this->createSession($userId, $bankId);
        }
        return $session;
    }

    private function createSession(string $userId, string $bankId)
    {
        $user = auth()->user();
        $res = $this->baseSessionRequest($userId, $bankId);
        $session = Session::create([
            'name' => $user->name,
            'user_id' => $userId,
            'bank_id' => $bankId,
            'session_id' => $res->sessionId,
        ]);
        return $session;
    }

    // private function deleteSession(string $userId, string $bankId) // ! not in use yet (auto delete session after completed transaction?)
    // {
    //     $user = auth()->user();
    //     $session = Session::where('name', $user->name)
    //         ->where('user_id', $userId)
    //         ->where('bank_id', $bankId)
    //         ->first();
    //     $session->delete();
    //     return $session;
    // }

    // private function updateSession(string $userId, string $bankId) // ! not needed?
    // {
    //     $user = auth()->user();
    //     $res = $this->baseSessionRequest($userId, $bankId);
    //     $session = Session::where('name', $user->name)
    //         ->where('user_id', $userId)
    //         ->where('bank_id', $bankId)
    //         ->first();
    //     $session->session_id = $res->sessionId;
    //     $session->save();
    //     return $session;
    // }

    private function baseSessionRequest(string $userId, string $bankId)
    {
        $user = auth()->user();
        $data = [
            'headers' => ['x-device-id' => $userId],
            'json' => ['bankId' => $bankId]
        ];
        $res = $this->apiHelper->request($user->access_token, 'POST', 'session', $data);
        return $res;
    }

    //---------//
    // Account //
    //---------//

    public function getAccounts(string $userId, string $bankId, string $personalNumber)
    {
        $url = 'accounts';
        $res = $this->baseAccountRequest($userId, $bankId, $personalNumber, $url);
        if ($this->needApproval($res)) return $res;
        $account = Account::jsonDeserialize($res);
        return $account;
    }

    public function getAccountByID(string $userId, string $bankId, string $personalNumber, string $id)
    {
        $url = "accounts/{$id}";
        $res = $this->baseAccountRequest($userId, $bankId, $personalNumber, $url);
        if ($this->needApproval($res)) return $res;
        $account = Account::jsonDeserialize($res);
        return $account;
    }

    public function getAccountBalancesByID(string $userId, string $bankId, string $personalNumber, string $id)
    {
        $url = "accounts/{$id}/balances";
        return $this->baseAccountRequest($userId, $bankId, $personalNumber, $url);
    }

    public function getAccountTransactionsByID(string $userId, string $bankId, string $personalNumber, string $id)
    {
        $url = "accounts/{$id}/transactions";
        return $this->baseAccountRequest($userId, $bankId, $personalNumber, $url);
    }

    private function baseAccountRequest(string $userId, string $bankId, string $personalNumber, string $url)
    {
        $user = auth()->user();
        $data = $this->getAccountRequestData($userId, $bankId, $personalNumber, $user->encryption_key);
        try {
            return $this->apiHelper->request($user->access_token, 'GET', $url, $data);
        } catch (ConsentRequiredException $e) {
            $url = $e->getConsentUrl();
            return $this->getConsent($url, $data);
        }
    }

    private function getAccountRequestData(string $userId, string $bankId, string $personalNumber, string $encryptionKey)
    {
        $session = $this->getOrCreateSession($userId, $bankId);
        $data['headers'] = [
            'x-device-id' => $userId,
            'x-session-id' => $session->session_id,
            'x-psu-ip-address' => request()->ip()
        ];
        if ($this->isIdentificationRequire($bankId)) {
            if (!$personalNumber) throw new JsonException(400, 'x-identification-id is required');
            $data['headers']['x-psu-id'] = $this->encryptIdentifier($encryptionKey, $personalNumber);
        }
        return $data;
    }

    //---------//
    // Payment //
    //---------//

    public function newPayment(string $userId, string $bankId, string $personalNumber, object $json)
    {
        $url = 'payments/domestic-transfer';
        $user = auth()->user();
        $session = $this->getOrCreateSession($userId, $bankId);
        $data = [
            'headers' => [
                'x-device-id' => $userId,
                'x-session-id' => $session->session_id,
                'x-redirect-url' => $user->redirect_url,
                'x-psu-ip-address' => request()->ip(),
            ],
            'json' => [
                'debtorAccount' => ['bban' => $json->debtor_account->bban],
                'debtorName' => $json->debtor_account->owner,
                'creditorAccount' => ['bban' => $json->creditor_account->bban],
                'creditorName' => $json->creditor_account->owner,
                'currency' => strtoupper($json->currency),
                'instrumentedAmount' => $json->amount,
                'endToEndIdentification' => $json->identification,
                'remittanceInformationUnstructured' => property_exists($json, 'note') ? $json->note : '',
                'paymentMetadata' => new stdClass(),
            ],
        ];
        if ($this->isIdentificationRequire($bankId)) {
            if (!$personalNumber) throw new JsonException(400, 'x-identification-id is required');
            $data['headers']['x-psu-id'] = $this->encryptIdentifier($user->encryption_key, $personalNumber);
        }
        try {
            $this->apiHelper->request($user->access_token, 'POST', $url, $data);
            return [
                'user_id' => $userId,
                'action' => 'Payment authorized.',
            ];
        } catch (ConsentRequiredException $e) {
            Payment::create([
                'name' => $user->name,
                'session_id' => $session->session_id,
                'payment_id' => $e->getMetaId(),
            ]);
            $url = $e->getConsentUrl();
            return $this->getConsent($url, $data);
        }
    }

    public function completePayment(string $id, string $userId, string $sessionId)
    {
        $user = auth()->user();
        $url = "payments/domestic-transfer/{$id}/complete";
        $data = [
            'headers' => [
                'x-device-id' => $userId,
                'x-session-id' => $sessionId,
                'x-psu-ip-address' => request()->ip(),
            ],
            'json' => [],
        ];
        $this->apiHelper->request($user->access_token, 'POST', $url, $data);
        return true;
    }

    //---------//
    // Helpers //
    //---------//

    private function isIdentificationRequire(string $bankId)
    {
        $bank = $this->getBankByID($bankId);
        return $bank->personalIdentificationRequired;
    }

    private function encryptIdentifier(string $encryptionKey, string $personalNumber)
    {
        $data_to_encrypt = $personalNumber; // sandbox value for DNB - 31125461037
        $cipher = "aes-128-gcm";
        $raw_data = $encryptionKey; // value of the rawValue field from the encryption key
        $key = base64_decode($raw_data);
        if (in_array($cipher, openssl_get_cipher_methods())) {
            $iv_len = openssl_cipher_iv_length($cipher);
            $iv = openssl_random_pseudo_bytes($iv_len);
            $tag = "";
            $ciphertext = openssl_encrypt($data_to_encrypt, $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag);
            $with_iv = base64_encode($iv . $ciphertext . $tag);
            return $with_iv;
        }
        throw new JsonException(500, 'Invalid encryption cipher, please contact us');
    }

    private function needApproval($res)
    {
        if (is_array($res) && array_key_exists('href', $res)) {
            return true;
        }
        return false;
    }
}
