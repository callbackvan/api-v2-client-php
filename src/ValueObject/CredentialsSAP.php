<?php

namespace CallbackHunterAPIv2\ValueObject;

class CredentialsSAP implements CredentialsInterface
{
    /** @var string */
    private $token;

    /**
     * Credentials constructor.
     *
     * @param string $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return [
            'Authorization' => 'Bearer '.$this->token,
        ];
    }
}
