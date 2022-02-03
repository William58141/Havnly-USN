<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    private string $countryCode;
    private string $bankingGroupName;
    private bool $personalIdentificationRequired;
    private string $id;
    private string $bankDisplayName;
    private array $supportedServices;
    private string $bic;
    private string $bankOfficialName;
    private string $status;

    public function __construct(string $countryCode, string $bankingGroupName, bool $personalIdentificationRequired, string $id, string $bankDisplayName, array $supportedServices, string $bic, string $bankOfficialName, $status)
    {
        $this->countryCode = $countryCode;
        $this->bankingGroupName = $bankingGroupName;
        $this->personalIdentificationRequired = $personalIdentificationRequired;
        $this->id = $id;
        $this->bankDisplayName = $bankDisplayName;
        $this->supportedServices = $supportedServices;
        $this->bic = $bic;
        $this->bankOfficialName = $bankOfficialName;
        $this->status = $status;
    }

    public function jsonSerialize()
    {
        return [
            'countryCode' => $this->countryCode,
            'name' => $this->bankDisplayName,
            'id' => $this->id,
            'status' =>$this->status,
        ];
    }

    public static function jsonDeserialize($json)
    {
        if (is_array($json)) {
            $banks = [];
            foreach ($json as $bank) {
                $bank = new Bank($bank->countryCode, $bank->bankingGroupName, $bank->personalIdentificationRequired, $bank->id, $bank->bankDisplayName, $bank->supportedServices, $bank->bic, $bank->bankOfficialName, $bank->status);
                array_push($banks, $bank);
            }
            return $banks;
        }
        return new Bank($json->countryCode, $json->bankingGroupName, $json->personalIdentificationRequired, $json->id, $json->bankDisplayName, $json->supportedServices, $json->bic, $json->bankOfficialName, $json->status);
    }
}
