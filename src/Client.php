<?php

namespace CallbackHunterAPIv2;

use CallbackHunterAPIv2\Type\FileForUploadInterface;
use CallbackHunterAPIv2\ValueObject\CredentialsInterface;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

class Client implements ClientInterface
{
    const VERSION = '1.0.4';
    const BASE_URI = 'https://callbackhunter.com/api/v2/';
    const CONTENT_TYPE = 'application/hal+json';

    /** @var string */
    private $baseUri = self::BASE_URI;

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
            $this->buildOptions(
                $options,
                ['Content-Type' => self::CONTENT_TYPE]
            )
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
            RequestOptions::JSON  => $data,
            RequestOptions::QUERY => $query,
        ];

        return $this->client->request(
            'post',
            $this->buildUri($path),
            $this->buildOptions(
                $options,
                ['Content-Type' => self::CONTENT_TYPE]
            )
        );
    }

    /**
     * Загрузка файлов
     *
     * @param string                 $path
     * @param FileForUploadInterface $image
     * @param array                  [$data = []]
     *
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function uploadFile(
        $path,
        FileForUploadInterface $image,
        array $data = []
    ) {
        $options = [
            RequestOptions::MULTIPART => [
                [
                    'name'     => $image->getName(),
                    'contents' => $image->getStream(),
                ],
            ],
        ];

        $options[RequestOptions::MULTIPART][] = [
            'name'     => 'data',
            'contents' => json_encode($data),
        ];

        return $this->client->request(
            'post',
            $this->buildUri($path),
            $this->buildOptions($options)
        );
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function buildUri($path)
    {
        $result = trim($path, '/');
        if (!$this->client->getConfig('base_uri')) {
            $result = $this->baseUri.$result;
        }

        return $result;
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
            'User-Agent' => 'CallbackHunterAPIv2Client/'.self::VERSION,
            'Accept'     => implode(
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
            RequestOptions::HTTP_ERRORS     => false,
        ];

        return array_replace($default, $options);
    }
}
