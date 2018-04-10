<?php
/**
 * Created by PhpStorm.
 * User: vdvug_000
 * Date: 09.04.2018
 * Time: 18:44
 */

namespace CallbackHunterAPIv2\Tests\ValueObject;

use CallbackHunterAPIv2\ValueObject\CredentialsSAP;

class CredentialsSAPTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \CallbackHunterAPIv2\ValueObject\CredentialsSAP::__construct
     * @covers \CallbackHunterAPIv2\ValueObject\CredentialsSAP::getHeaders
     */
    public function testGetHeaders()
    {
        $token = 'token';
        $expected = [
            'Authorization' => "Bearer {$token}",
        ];
        $credentials = new CredentialsSAP($token);
        $this->assertEquals($expected, $credentials->getHeaders());
    }
}
