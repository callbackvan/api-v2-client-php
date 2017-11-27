<?php

namespace CallbackHunterAPIv2;

use CallbackHunterAPIv2\Type\FileForUploadInterface;
use CallbackHunterAPIv2\ValueObject\CredentialsInterface;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

class Client implements ClientInterface
{
    const VERSION = '0.1.0';

    /** @var string */
    private $baseUri = 'https://callbackhunter.com/api/v2/';

    /** @var \GuzzleHttp\ClientInterface */
    private $client;

    /** @var CredentialsInterface */
    private $credentials;

    /**
     * Client constructor.
     *
     * @param \GuzzleHttp\ClientInterface $client
     * @param CredentialsInterface        $credentials
     */
    public function __construct(
        \GuzzleHttp\ClientInterface $client,
        CredentialsInterface $credentials
    ) {
        $this->client = $client;
        $this->credentials = $credentials;
    }


    /**
     * Отправка запроса на получение информации
     *
     * @param string $path
     * @param array  $query
     *
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestGet($path, array $query = [])
    {
        $options = [
            RequestOptions::QUERY => $query,
        ];

        return $this->client->request(
            'get',
            $this->buildUri($path),
            $this->buildOptions($options)
        );
    }

    /**
     * Отправка запроса на изменение информации
     *
     * @param string $path
     * @param array  $data
     * @param array  $query
     *
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestPost($path, array $data = [], array $query = [])
    {
        $options = [
            RequestOptions::BODY  => json_encode($data),
            RequestOptions::QUERY => $query,
        ];

        return $this->client->request(
            'post',
            $this->buildUri($path),
            $this->buildOptions($options)
        );
    }

    /**
     * Загрузка файлов
     *
     * @param string                 $path
     * @param FileForUploadInterface $image
     *
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function uploadFile($path, FileForUploadInterface $image)
    {
        $options = [
            RequestOptions::MULTIPART => [
                [
                    'name'     => $image->getName(),
                    'contents' => $image->getStream(),
                ],
            ],
        ];

        return $this->client->request(
            'post',
            $this->buildUri($path),
            $this->buildOptions(
                $options,
                ['Content-Type' => 'multipart/form-data']
            )
        );
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function buildUri($path)
    {
        return $this->baseUri.trim($path, '/');
    }

    /**
     * @param array $options
     * @param array $headers
     *
     * @return array
     */
    private function buildOptions(array $options, array $headers = [])
    {
        $defaultHeaders = [
            'Content-Type' => 'application/hal+json',
            'User-Agent'   => 'CallbackHunterAPIv2Client/'.self::VERSION,
            'Accept'       => implode(
                ',',
                [
                    'application/json',
                    'application/hal+json',
                    'application/problem+json',
                ]
            ),
        ];

        $default = [
            RequestOptions::ALLOW_REDIRECTS => true,
            RequestOptions::HEADERS         => array_replace(
                $defaultHeaders,
                $this->credentials->getHeaders(),
                $headers
            ),
            RequestOptions::CONNECT_TIMEOUT => 10,
            RequestOptions::TIMEOUT         => 60,
        ];

        return array_replace($default, $options);
    }
}
