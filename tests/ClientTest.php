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
     * @covers       \CallbackHunterAPIv2\Client::__construct
     * @covers       \CallbackHunterAPIv2\Client::requestGet
     * @covers       \CallbackHunterAPIv2\Client::buildUri
     * @covers       \CallbackHunterAPIv2\Client::buildOptions
     * @dataProvider baseUriProvider
     *
     * @param string $baseUri
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testRequestGet($baseUri)
    {
        $credentials = $this->prepareCredentials();

        $path = 'test';
        $query = [
            'some' => 'params',
            'of'   => 'query',
        ];

        $this->expectClientCheckBaseUri($baseUri);

        $response = $this->createMock(ResponseInterface::class);
        $this->guzzleClient
            ->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('get'),
                $this->equalTo($this->buildPath($baseUri, $path)),
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
     * @covers       \CallbackHunterAPIv2\Client::__construct
     * @covers       \CallbackHunterAPIv2\Client::requestPost
     * @covers       \CallbackHunterAPIv2\Client::buildUri
     * @covers       \CallbackHunterAPIv2\Client::buildOptions
     * @dataProvider baseUriProvider
     *
     * @param string $baseUri
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testRequestPost($baseUri)
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

        $this->expectClientCheckBaseUri($baseUri);

        $response = $this->createMock(ResponseInterface::class);
        $this->guzzleClient
            ->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('post'),
                $this->equalTo($this->buildPath($baseUri, $path)),
                $this->equalTo(
                    $this->defaultOptions +
                    [
                        RequestOptions::HEADERS => $this->defaultHeaders
                            + $credentials,
                    ] +
                    [
                        RequestOptions::QUERY => $query,
                        RequestOptions::JSON  => $data,
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
     * @covers       \CallbackHunterAPIv2\Client::__construct
     * @covers       \CallbackHunterAPIv2\Client::uploadFile
     * @covers       \CallbackHunterAPIv2\Client::buildUri
     * @covers       \CallbackHunterAPIv2\Client::buildOptions
     * @dataProvider baseUriProvider
     *
     * @param string $baseUri
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testUploadImage($baseUri)
    {
        $credentials = $this->prepareCredentials();
        $additionalData = [
            'foo' => 'bar',
            'baz' => 12,
        ];
        $additionalDataMultiPart = [
            [
                'name'     => 'foo',
                'contents' => $additionalData['foo'],
            ],
            [
                'name'     => 'baz',
                'contents' => $additionalData['baz'],
            ],
        ];

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

        $this->expectClientCheckBaseUri($baseUri);

        $response = $this->createMock(ResponseInterface::class);
        $headers = $this->defaultHeaders;
        unset($headers['Content-Type']);

        $this->guzzleClient
            ->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('post'),
                $this->equalTo($this->buildPath($baseUri, $path)),
                $this->equalTo(
                    $this->defaultOptions +
                    [
                        RequestOptions::HEADERS =>
                            $headers +
                            $credentials,
                    ] +
                    [
                        RequestOptions::MULTIPART =>
                            array_merge(
                                [
                                    [
                                        'name'     => $imageName,
                                        'contents' => $imageStream,
                                    ],
                                ],
                                $additionalDataMultiPart
                            ),
                    ]
                )
            )
            ->willReturn($response);

        $this->assertSame(
            $response,
            $this->client->uploadFile($path, $imageForUpload, $additionalData)
        );
    }

    public function baseUriProvider()
    {
        return [
            [Client::BASE_URI],
            ['http://example.com'],
        ];
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
            RequestOptions::HTTP_ERRORS     => false,
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

    /**
     * Ожидание, что будет проверка на то, что base_uri же установлен
     *
     * @param string $baseUri
     */
    private function expectClientCheckBaseUri($baseUri)
    {
        $this->guzzleClient
            ->expects($this->once())
            ->method('getConfig')
            ->with('base_uri')
            ->willReturn($baseUri === Client::BASE_URI ? null : $baseUri);
    }

    /**
     * @param string $baseUri
     * @param string $path
     *
     * @return string
     */
    private function buildPath($baseUri, $path)
    {
        return $baseUri === Client::BASE_URI ? $baseUri.$path : $path;
    }
}
