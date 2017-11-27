<?php

namespace CallbackHunterAPIv2;

use CallbackHunterAPIv2\Type\FileForUploadInterface;
use Psr\Http\Message\ResponseInterface;

interface ClientInterface
{
    /**
     * @param string $path
     * @param array  $query
     *
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestGet($path, array $query = []);

    /**
     * @param string $path
     * @param array  $data
     * @param array  $query
     *
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestPost($path, array $data = [], array $query = []);

    /**
     * @param string                 $path
     * @param FileForUploadInterface $image
     *
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function uploadFile($path, FileForUploadInterface $image);
}
