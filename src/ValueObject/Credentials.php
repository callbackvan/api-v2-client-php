<?php

namespace CallbackHunterAPIv2\ValueObject;

class Credentials implements CredentialsInterface
{
    /** @var integer */
    private $userId;
    /** @var string */
    private $APIkey;

    /**
     * Credentials constructor.
     *
     * @param int    $userId
     * @param string $APIkey
     */
    public function __construct($userId, $APIkey)
    {
        $this->userId = $userId;
        $this->APIkey = $APIkey;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return [
            'X-CBH-Auth-UserId' => $this->userId,
            'X-CBH-Auth-APIkey' => $this->APIkey,
        ];
    }
}
