<?php

namespace App\Exceptions\Api;

use Exception;

class ConsentRequiredException extends Exception
{
    protected $body;

    public function __construct(object $body)
    {
        $this->body = $body;
    }

    public function getConsentUrl()
    {
        foreach ($this->body->links as $link) {
            if ($link->rel === 'consent') {
                return $link->href;
            }
        }
        return null;
    }
}
