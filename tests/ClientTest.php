<?php

namespace CallbackHunterAPIv2\Tests;

use CallbackHunterAPIv2\Client;
use CallbackHunterAPIv2\Type\FileForUploadInterface;
use CallbackHunterAPIv2\ValueObject\CredentialsInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ClientTest extends TestCase
{
    /** @var Client */
    private $client;
    /** @var ClientInterface */
    private $guzzleClient;
    /** @var CredentialsInterface */
    private $credentials;
    /** @var array */
    private $defaultOptions;
    /** @var array */
    private $defaultHeaders;

    /**
     * @covers \CallbackHunterAPIv2\Client::__construct
     * @covers \CallbackHunterAPIv2\Client::requestGet
     * @covers \CallbackHunterAPIv2\Client::buildUri
     * @covers \CallbackHunterAPIv2\Client::buildOptions
     */
    public function testRequestGet()
    {
        $credentials = $this->prepareCredentials();

        $path = 'test';
        $query = [
            'some' => 'params',
            'of'   => 'query',
        ];

        $response = $this->createMock(ResponseInterface::class);
        $this->guzzleClient
            ->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('get'),
                $this->equalTo('https://callbackhunter.com/api/v2/'.$path),
                $this->equalTo(
                    $this->defaultOptions +
                    [
                        RequestOptions::HEADERS => $this->defaultHeaders
                            + $credentials,
                    ] +
                    [
                        RequestOptions::QUERY => $query,
                    ]
                )
            )
            ->willReturn($response);

        $this->assertSame($response, $this->client->requestGet($path, $query));
    }

    /**
     * @covers \CallbackHunterAPIv2\Client::__construct
     * @covers \CallbackHunterAPIv2\Client::requestPost
     * @covers \CallbackHunterAPIv2\Client::buildUri
     * @covers \CallbackHunterAPIv2\Client::buildOptions
     */
    public function testRequestPost()
    {
        $credentials = $this->prepareCredentials();

        $path = 'test';
        $query = [
            'some' => 'params',
            'of'   => 'query',
        ];
        $data = [
            'any' => 'time',
            'or'  => 'any',
            'way' => 'first',
        ];

        $response = $this->createMock(ResponseInterface::class);
        $this->guzzleClient
            ->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('post'),
                $this->equalTo('https://callbackhunter.com/api/v2/'.$path),
                $this->equalTo(
                    $this->defaultOptions +
                    [
                        RequestOptions::HEADERS => $this->defaultHeaders
                            + $credentials,
                    ] +
                    [
                        RequestOptions::QUERY => $query,
                        RequestOptions::BODY  => json_encode($data),
                    ]
                )
            )
            ->willReturn($response);

        $this->assertSame(
            $response,
            $this->client->requestPost($path, $data, $query)
        );
    }

    /**
     * @covers \CallbackHunterAPIv2\Client::__construct
     * @covers \CallbackHunterAPIv2\Client::uploadFile
     * @covers \CallbackHunterAPIv2\Client::buildUri
     * @covers \CallbackHunterAPIv2\Client::buildOptions
     */
    public function testUploadImage()
    {
        $credentials = $this->prepareCredentials();

        $path = 'test';

        $imageName = 'some name';
        $imageStream = $this->createMock(StreamInterface::class);
        $imageForUpload = $this->createMock(FileForUploadInterface::class);
        $imageForUpload
            ->expects($this->once())
            ->method('getName')
            ->willReturn($imageName);
        $imageForUpload
            ->expects($this->once())
            ->method('getStream')
            ->willReturn($imageStream);

        $response = $this->createMock(ResponseInterface::class);
        $this->guzzleClient
            ->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('post'),
                $this->equalTo('https://callbackhunter.com/api/v2/'.$path),
                $this->equalTo(
                    $this->defaultOptions +
                    [
                        RequestOptions::HEADERS => [
                                'Content-Type' => 'multipart/form-data',
                            ] +
                            $this->defaultHeaders +
                            $credentials,
                    ] +
                    [
                        RequestOptions::MULTIPART => [
                            [
                                'name'     => $imageName,
                                'contents' => $imageStream,
                            ],
                        ],
                    ]
                )
            )
            ->willReturn($response);

        $this->assertSame(
            $response,
            $this->client->uploadFile($path, $imageForUpload)
        );
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->guzzleClient = $this->createMock(ClientInterface::class);
        $this->credentials = $this->createMock(CredentialsInterface::class);
        $this->client = new Client($this->guzzleClient, $this->credentials);

        $this->defaultOptions = [
            RequestOptions::ALLOW_REDIRECTS => true,
            RequestOptions::CONNECT_TIMEOUT => 10,
            RequestOptions::TIMEOUT         => 60,
        ];

        $this->defaultHeaders = [
            'Content-Type' => 'application/hal+json',
            'User-Agent'   => 'CallbackHunterAPIv2Client/'.Client::VERSION,
            'Accept'       => implode(
                ',',
                [
                    'application/json',
                    'application/hal+json',
                    'application/problem+json',
                ]
            ),
        ];
    }

    /**
     * @return array
     */
    private function prepareCredentials()
    {
        $credentials = [
            'foo' => 'bar',
            'baz' => 'foo',
        ];
        $this->credentials
            ->expects($this->once())
            ->method('getHeaders')
            ->willReturn($credentials);

        return $credentials;
    }
}
