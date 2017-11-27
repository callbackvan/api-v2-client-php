<?php

namespace CallbackHunterAPIv2\Tests\ValueObject;

use CallbackHunterAPIv2\ValueObject\Credentials;
use PHPUnit\Framework\TestCase;

class CredentialsTest extends TestCase
{
    /**
     * @covers \CallbackHunterAPIv2\ValueObject\Credentials::__construct
     * @covers \CallbackHunterAPIv2\ValueObject\Credentials::getHeaders
     */
    public function testGetHeaders()
    {
        $userId = 123;
        $key = md5('test');
        $expected = [
            'X-CBH-Auth-UserId' => $userId,
            'X-CBH-Auth-APIkey' => $key,
        ];
        $credentials = new Credentials($userId, $key);
        $this->assertEquals($expected, $credentials->getHeaders());
    }
}
