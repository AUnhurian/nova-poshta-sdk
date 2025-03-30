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

## Підхід до мокування

У тестах використовується бібліотека Mockery для мокування HTTP-відповідей від API Нової Пошти. Цей підхід дозволяє тестувати без реальних API-запитів.

Приклад мокування HTTP-відповідей:

```php
// Створення мок HTTP-клієнта
$httpClient = Mockery::mock(NovaPoshtaHttpClient::class);

// Визначення очікуваного запиту та відповіді
$httpClient->shouldReceive('request')
    ->with('Address', 'getAreas', [])
    ->andReturn([
        'success' => true,
        'data' => [
            ['Ref' => '123', 'Description' => 'Область 1'],
            ['Ref' => '456', 'Description' => 'Область 2'],
        ],
    ]);

// Впровадження мок-клієнта в клас API
$addressApi = new AddressApi($httpClient);

// Тестування методу
$result = $addressApi->getAreas();
```

## Використання фейкових відповідей в інтеграційних тестах

Окрім підходу з використанням Mockery, описаного вище, який використовується внутрішньо для юніт-тестів SDK, SDK також надає вбудований механізм для налаштування фейкових відповідей, який можна використовувати у власних інтеграційних тестах.

Цей підхід зручніший, коли ви хочете тестувати код вашого додатку, який використовує Nova Poshta SDK, оскільки він не вимагає модифікації внутрішніх компонентів SDK.

```php
// Створення екземпляру SDK з вашим ключем API
$sdk = new \AUnhurian\NovaPoshta\SDK\NovaPoshtaSDK('your_api_key');

// Налаштування фейкових відповідей
$mockResponses = [
    // Визначення фейкової відповіді для Address.getAreas
    'Address.getAreas' => [
        'response' => [
            'success' => true,
            'data' => [
                ['Ref' => '123', 'Description' => 'Область 1'],
                ['Ref' => '456', 'Description' => 'Область 2'],
            ],
            'errors' => [],
            'warnings' => [],
            'info' => []
        ],
    ],
    
    // Фейкова відповідь для getCities з перевіркою параметрів
    'Address.getCities' => [
        'params' => [
            'FindByString' => 'Тестове місто',
        ],
        'response' => [
            'success' => true,
            'data' => [
                ['Ref' => '789', 'Description' => 'Тестове місто'],
            ],
            'errors' => [],
            'warnings' => [],
            'info' => []
        ],
    ],
];

// Застосування фейкових відповідей до SDK
$sdk->setMockResponses($mockResponses);

// Тепер всі API-запити будуть використовувати фейкові дані
$areas = $sdk->address()->getAreas();
// $areas буде містити фейкові дані, визначені вище

// Очищення після тестування
$sdk->clearMockResponses();
```

Система фейкових відповідей підтримує перевірку параметрів, що дозволяє визначати різні відповіді для одного й того ж методу API на основі вхідних параметрів. Це особливо корисно для тестування різних сценаріїв:

```php
$mockResponses = [
    // Різні відповіді для getCities на основі параметрів
    'Address.getCities' => [
        'params' => [
            'FindByString' => 'Київ',
        ],
        'response' => [
            'success' => true,
            'data' => [
                ['Ref' => '123', 'Description' => 'Київ'],
            ],
            'errors' => [],
            'warnings' => [],
            'info' => []
        ],
    ],
    
    // Інший мок для того ж методу, але з іншими параметрами
    'Address.getCities' => [
        'params' => [
            'FindByString' => 'Львів',
        ],
        'response' => [
            'success' => true,
            'data' => [
                ['Ref' => '456', 'Description' => 'Львів'],
            ],
            'errors' => [],
            'warnings' => [],
            'info' => []
        ],
    ],
    
    // Мок для відповіді з помилкою
    'Address.getCities' => [
        'params' => [
            'FindByString' => 'Error',
        ],
        'response' => [
            'success' => false,
            'data' => [],
            'errors' => ['Місто не знайдено'],
            'warnings' => [],
            'info' => []
        ],
        'statusCode' => 400,
    ],
];

$sdk->setMockResponses($mockResponses);

// Кожен виклик отримає різну відповідь на основі параметрів
$kyivCities = $sdk->address()->getCities(null, 'Київ');
$lvivCities = $sdk->address()->getCities(null, 'Львів');

// Запит, який призведе до винятку
$errorCities = $sdk->address()->getCities(null, 'Error');
```

### Найкращі практики для фейкових відповідей

1. **Очищайте моки після тестів**: Завжди очищайте фейкові відповіді після кожного тесту, щоб запобігти впливу між тестами.

2. **Відповідайте структурі API**: Переконайтеся, що ваші фейкові відповіді відповідають структурі реальних відповідей API Нової Пошти.

3. **Тестуйте сценарії помилок**: Використовуйте фейкові відповіді для тестування того, як ваш код обробляє помилки API, встановлюючи `'success' => false`.

4. **Використовуйте перевірку параметрів**: При тестуванні методів, які приймають різні параметри, налаштовуйте різні фейкові відповіді для кожної комбінації параметрів.

5. **Встановлюйте відповідні коди статусу**: Для відповідей з помилками встановлюйте відповідні HTTP-коди статусу для симуляції реальних умов.

## Написання нових тестів

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