<?php

namespace CallbackHunterAPIv2\Type;

use Psr\Http\Message\StreamInterface;

interface FileForUploadInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return StreamInterface
     */
    public function getStream();
}
