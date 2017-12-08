<?php

namespace CallbackHunterAPIv2\Tests;

use CallbackHunterAPIv2\Client;
use CallbackHunterAPIv2\ClientFactory;
use CallbackHunterAPIv2\ValueObject\Credentials;
use PHPUnit\Framework\TestCase;

class ClientFactoryTest extends TestCase
{
    /** @var ClientFactory */
    private $factory;

    /**
     * @covers \CallbackHunterAPIv2\ClientFactory::makeWithAPICredentials
     */
    public function testMakeWithAPICredentials()
    {
        $userId = 123;
        $APIKey = 'test';
        $credentials = new Credentials($userId, $APIKey);

        $expected = new Client(new \GuzzleHttp\Client, $credentials);
        $result = $this->factory->makeWithAPICredentials($userId, $APIKey);

        $this->assertEquals($expected, $result);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->factory = new ClientFactory;
    }
}
