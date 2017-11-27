<?php

namespace CallbackHunterAPIv2\ValueObject;

interface CredentialsInterface
{
    /**
     * Возвращает
     *
     * @return array [
     *   X-CBH-Auth-UserId => 123
     *   X-CBH-Auth-APIkey => someUglyString
     * ]
     */
    public function getHeaders();
}
