# CallbackHunter APIv2 Client
Официальный клиент для APIv2 CallbackHunter.
### Status
[![Build Status](https://travis-ci.org/callbackvan/api-v2-client-php.svg?branch=master)](https://travis-ci.org/callbackvan/api-v2-client-php)
[![Coverage Status](https://coveralls.io/repos/github/callbackvan/api-v2-client-php/badge.svg)](https://coveralls.io/github/callbackvan/api-v2-client-php)


Документацию по доступным методам можно найти по [ссылке](https://developers.callbackhunter.com)

_*Внимание!*_ API находится в стадии разработки.

## Installation
Для того, чтобы подключить библиотеку в свой проект, можно воспользоваться [composer](https://getcomposer.org)

```bash
composer require callbackhunter/apiv2client
```

## Usage
Пример использования для получения списка виджетов

```php
use CallbackHunterAPIv2\ValueObject\Credentials as CBHCredentials;
use CallbackHunterAPIv2\Client as CBHClient;

$credentials = new CBHCredentials($userId, $key);
$client = new CBHClient($credentials, new \GuzzleHttp\Client);
$response = $client->requestGet('widgets');
if ($response->getStatusCode() === 200) {
    $widgetsInfo = json_decode((string)$response->getBody(), true);
    $widgets = $widgetsInfo['_embedded']['widgets'];
}
```
