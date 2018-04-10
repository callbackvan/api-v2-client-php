<?php

namespace CallbackHunterAPIv2;

use CallbackHunterAPIv2\ValueObject\Credentials;
use CallbackHunterAPIv2\ValueObject\CredentialsSAP;

class ClientFactory
{
    /**
     * @param integer $userId
     * @param string  $APIkey
     * @param array   $config
     *
     * @return Client
     */
    public function makeWithAPICredentials($userId, $APIkey, array $config = [])
    {
        $client = new \GuzzleHttp\Client($config);
        $credentials = new Credentials($userId, $APIkey);

        return new Client($client, $credentials);
    }

    /**
     * @param string $token
     * @param array  $config
     *
     * @return Client
     */
    public function makeWithSAPCredentials($token, array $config = [])
    {
        $client = new \GuzzleHttp\Client($config);
        $credentials = new CredentialsSAP($token);

        return new Client($client, $credentials);
    }
}
