# Тестування SDK Нової Пошти

Цей документ описує процес тестування SDK Нової Пошти.

## Вимоги

- PHP 7.4+
- Composer
- PHPUnit (для тестування)
- Mockery (для мокінгу HTTP-клієнта)

## Запуск тестів

Для запуску тестів використовуйте:

```bash
# Запуск тестів через PHPUnit
vendor/bin/phpunit
```

Для запуску окремого тесту:

```bash
# Запуск окремого тесту через PHPUnit
vendor/bin/phpunit tests/Unit/Api/AddressApiTest.php
```

## Структура тестів

Проект використовує PHPUnit для тестування. Тести організовані за структурою:

```
tests/
├── Unit/
│   ├── Api/
│   │   ├── AddressApiTest.php
│   │   ├── BaseApiTest.php
│   │   ├── CommonApiTest.php
│   │   ├── CounterpartyApiTest.php
│   │   ├── DocumentApiTest.php
│   │   └── TrackingApiTest.php
│   ├── Http/
│   │   └── NovaPoshtaHttpClientTest.php
│   └── NovaPoshtaSDKTest.php
└── TestCase.php
```

## Конфігурація тестового середовища

Конфігурація PHPUnit знаходиться у файлі `phpunit.xml`. 
Тести запускаються з використанням стандартного автозавантажувача Composer.

```xml
<!-- phpunit.xml -->
<phpunit bootstrap="tests/autoload.php" colors="true">
    <testsuites>
        <testsuite name="Unit">
            <directory>./tests/Unit</directory>
        </testsuite>
    </testsuites>
    <filter>
        <whitelist>
            <directory>./app</directory>
        </whitelist>
    </filter>
    <php>
        <env name="APP_ENV" value="testing"/>
    </php>
</phpunit>
```

## Автозавантаження

Проєкт використовує стандартний PSR-4 автозавантажувач Composer. Простір імен `AUnhurian\NovaPoshta\SDK\` відповідає директорії `app/` згідно з налаштуваннями в `composer.json`:

```json
"autoload": {
    "psr-4": {
        "AUnhurian\\NovaPoshta\\SDK\\": "app/"
    }
},
"autoload-dev": {
    "psr-4": {
        "Tests\\": "tests/"
    }
}
```

## Додавання нових тестів

Для додавання нових тестів:

1. Створіть новий файл у відповідній директорії `tests/Unit/`
2. Унаслідуйтеся від `Tests\TestCase`
3. Використовуйте Mockery для мокування HTTP-клієнта
4. Запустіть тести для перевірки

### Приклад тесту

```php
<?php

namespace Tests\Unit\Api;

use Mockery;
use AUnhurian\NovaPoshta\SDK\Api\AddressApi;
use AUnhurian\NovaPoshta\SDK\Http\NovaPoshtaHttpClient;
use Tests\TestCase;

class AddressApiTest extends TestCase
{
    private $httpClientMock;
    private AddressApi $addressApi;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClientMock = Mockery::mock(NovaPoshtaHttpClient::class);
        $this->addressApi = new AddressApi($this->httpClientMock);
    }

    public function testGetAreas(): void
    {
        $mockResponse = [
            'success' => true,
            'data' => [
                [
                    'Ref' => 'area1',
                    'Description' => 'Київська',
                ],
            ],
        ];

        $this->httpClientMock
            ->shouldReceive('request')
            ->once()
            ->with('Address', 'getAreas', [])
            ->andReturn($mockResponse);

        $result = $this->addressApi->getAreas();

        $this->assertEquals($mockResponse, $result);
    }
} 