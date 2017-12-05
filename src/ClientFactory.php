<?php

namespace CallbackHunterAPIv2;

use CallbackHunterAPIv2\ValueObject\Credentials;

class ClientFactory
{
    /**
     * @param integer $userId
     * @param string  $APIkey
     *
     * @return Client
     */
    public function makeWithAPICredentials($userId, $APIkey)
    {
        $client = new \GuzzleHttp\Client();
        $credentials = new Credentials($userId, $APIkey);

        return new Client($client, $credentials);
    }
}
