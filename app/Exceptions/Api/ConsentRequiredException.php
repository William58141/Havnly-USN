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
        $link = $this->getLink();
        return $link->href;
    }

    public function getMetaId()
    {
        $link = $this->getLink();
        return $link->meta->id;
    }

    private function getLink()
    {
        foreach ($this->body->links as $link) {
            if ($link->rel === 'consent' || $link->rel === 'payment') {
                return $link;
            }
        }
        throw new JsonException(404, 'Neonomics missing links, please retry.');
    }
}
