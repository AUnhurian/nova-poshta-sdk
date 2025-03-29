# Nova Poshta SDK для PHP

PHP SDK для інтеграції з API Нової Пошти

[![PHP Version](https://img.shields.io/badge/php-%3E%3D7.4-blue.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

## Вимоги

- PHP 7.4 або вище
- Composer

## Встановлення

```bash
composer require aunhurian/nova-poshta-sdk
```

Або клонуйте репозиторій:

```bash
git clone https://github.com/aunhurian/nova-poshta-sdk.git
cd nova-poshta-sdk
composer install
```

## Швидкий старт

```php
// Створення екземпляру SDK
$apiKey = 'ваш_api_ключ';
$sdk = new \AUnhurian\NovaPoshta\SDK\NovaPoshtaSDK($apiKey);

// Отримання списку міст
$cities = $sdk->address()->getCities(findByString: 'Київ');

// Отримання списку відділень
$warehouses = $sdk->address()->getWarehouses(
    cityRef: '8d5a980d-391c-11dd-90d9-001a92567626', // Ref Києва
    findByString: 'Відділення'
);

// Відстеження посилки
$trackingInfo = $sdk->tracking()->getStatusDocuments('59000000000000');
```

## Документація API Нової Пошти

Цей SDK базується на офіційному API Нової Пошти. Детальну документацію по API можна знайти за посиланням:
[Документація API Нової Пошти](https://developers.novaposhta.ua/documentation)

## Структура SDK

SDK розділений на модулі, кожен з яких відповідає за окрему частину API Нової Пошти:

- **AddressApi** - Робота з адресами, населеними пунктами, вулицями та відділеннями
- **CounterpartyApi** - Робота з контрагентами (клієнтами)
- **DocumentApi** - Створення, редагування та видалення накладних (ТТН)
- **TrackingApi** - Відстеження статусу посилок
- **CommonApi** - Отримання довідкової інформації

## Детальний опис модулів

### AddressApi

```php
// Отримання списку областей
$areas = $sdk->address()->getAreas();

// Пошук населених пунктів
$settlements = $sdk->address()->searchSettlements('Київ');

// Отримання списку міст з фільтрацією
$cities = $sdk->address()->getCities(findByString: 'Київ', page: 1, limit: 20);

// Отримання списку відділень
$warehouses = $sdk->address()->getWarehouses(
    cityRef: '8d5a980d-391c-11dd-90d9-001a92567626',
    findByString: 'Відділення',
    page: 1,
    limit: 20
);

// Отримання типів відділень
$warehouseTypes = $sdk->address()->getWarehouseTypes();

// Отримання вулиць у місті
$streets = $sdk->address()->getStreet(
    cityRef: '8d5a980d-391c-11dd-90d9-001a92567626',
    findByString: 'Хрещатик'
);
```

### CounterpartyApi

```php
// Створення нового контрагента (фізична особа)
$counterparty = $sdk->counterparty()->save(
    counterpartyType: 'PrivatePerson',
    firstName: 'Іван',
    lastName: 'Петренко',
    middleName: 'Васильович',
    phone: '380991234567',
    email: 'test@example.com'
);

// Створення нового контрагента (організація)
$counterparty = $sdk->counterparty()->save(
    counterpartyType: 'Organization',
    companyName: 'ТОВ "Компанія"',
    edrpou: '12345678'
);

// Пошук контрагентів
$counterparties = $sdk->counterparty()->getCounterparties(
    findByString: 'Пет',
    counterpartyProperty: 'Recipient',
    page: 1,
    limit: 20
);

// Отримання контактних осіб контрагента
$contactPersons = $sdk->counterparty()->getCounterpartyContactPersons(
    ref: '005056801329',
    counterpartyProperty: 'Recipient'
);
```

### DocumentApi

```php
// Розрахунок вартості доставки
$cost = $sdk->document()->getDocumentPrice(
    citySender: '8d5a980d-391c-11dd-90d9-001a92567626',
    cityRecipient: 'db5c88de-391c-11dd-90d9-001a92567626',
    weight: '1',
    serviceType: 'WarehouseWarehouse',
    cost: 500,
    cargoType: 1,
    seatsAmount: 1
);

// Розрахунок дати доставки
$date = $sdk->document()->getDocumentDeliveryDate(
    citySender: '8d5a980d-391c-11dd-90d9-001a92567626',
    cityRecipient: 'db5c88de-391c-11dd-90d9-001a92567626',
    serviceType: 'WarehouseWarehouse',
    dateTime: '01.01.2023'
);

// Отримання списку накладних
$documents = $sdk->document()->getDocumentList(
    dateTimeFrom: '01.01.2023',
    dateTimeTo: '01.02.2023',
    page: 1,
    limit: 100
);

// Створення нової накладної (спрощений приклад)
$document = $sdk->document()->save([
    'PayerType' => 'Sender',
    'PaymentMethod' => 'Cash',
    'CargoType' => 'Cargo',
    'Weight' => 1,
    'ServiceType' => 'WarehouseWarehouse',
    'SeatsAmount' => 1,
    'Description' => 'Опис вантажу',
    'Cost' => 500,
    'CitySender' => '8d5a980d-391c-11dd-90d9-001a92567626',
    'Sender' => '5ace4a2e-13ee-11e5-add9-005056887b8d',
    'SenderAddress' => '2a8c3606-ab5b-11e9-8094-005056881c6b',
    'ContactSender' => '57b4218d-16d7-11e5-add9-005056887b8d',
    'SendersPhone' => '380991234567',
    'CityRecipient' => 'db5c88de-391c-11dd-90d9-001a92567626',
    'Recipient' => '7da56392-b64b-11e4-a77a-005056887b8d',
    'RecipientAddress' => '7da56392-b64b-11e4-a77a-005056887b8d',
    'ContactRecipient' => '57b4218d-16d7-11e5-add9-005056887b8d',
    'RecipientsPhone' => '380991234567',
]);
```

### TrackingApi

```php
// Отримання статусу одного відправлення
$status = $sdk->tracking()->getStatusDocuments('59000000000000');

// Отримання статусу декількох відправлень
$statuses = $sdk->tracking()->getStatusDocumentsBatch([
    ['DocumentNumber' => '59000000000000'],
    ['DocumentNumber' => '59000000000001'],
]);

// Отримання повної історії відправлення
$history = $sdk->tracking()->getStatusHistory('59000000000000');
```

### CommonApi

```php
// Отримання типів вантажу
$cargoTypes = $sdk->common()->getCargoTypes();

// Отримання списку типів оплати
$paymentTypes = $sdk->common()->getTypesOfPayment();

// Отримання списку типів платників
$payerTypes = $sdk->common()->getTypesOfPayers();

// Отримання списку типів послуг
$serviceTypes = $sdk->common()->getServiceTypes();

// Отримання часових інтервалів доставки
$timeIntervals = $sdk->common()->getTimeIntervals(
    recipientCityRef: 'db5c88de-391c-11dd-90d9-001a92567626',
    dateTime: '01.01.2023'
);
```

## Клас відповіді API

SDK містить клас `NovaPoshtaResponse` для роботи з відповідями API Нової Пошти. За замовчуванням SDK повертає тільки дані з відповіді, але ви можете отримати повний об'єкт відповіді, що містить додаткову інформацію:

```php
// Отримання повної відповіді API
$response = $sdk->requestWithFullResponse('Address', 'getAreas', []);

// Отримання даних з відповіді
$data = $response->getData();

// Перевірка статусу відповіді
$isSuccess = $response->isSuccess();

// Отримання попереджень
$warnings = $response->getWarnings();

// Отримання інформаційних повідомлень
$info = $response->getInfo();

// Отримання статус-коду HTTP
$statusCode = $response->getStatusCode();

// Отримання сирих даних відповіді
$rawData = $response->getRawData();
```

Ви також можете використовувати прямий запит до API замість модульних методів:

```php
// Пряме використання API через SDK
$cities = $sdk->request('Address', 'getCities', ['FindByString' => 'Київ']);

// Пряме використання API з повною відповіддю
$response = $sdk->requestWithFullResponse('Address', 'getCities', ['FindByString' => 'Київ']);
$cities = $response->getData();
```

## Тестування

SDK має повний набір тестів для перевірки функціональності. Для запуску тестів використовуйте:

```bash
# Запуск тестів через PHPUnit
vendor/bin/phpunit
```

Тести організовані по модулях API та використовують мокування HTTP-запитів для імітації роботи з API Нової Пошти без реальних мережевих запитів.

Детальний опис системи тестування можна знайти у файлі [TESTING.md](TESTING.md).

## Виключення

SDK використовує систему виключень для обробки помилок:

- `NovaPoshtaApiException` - Виключення, що виникає при помилках в API Нової Пошти
- `NovaPoshtaHttpException` - Виключення, що виникає при HTTP-помилках

```php
try {
    $result = $sdk->address()->getAreas();
} catch (AUnhurian\NovaPoshta\SDK\Exceptions\NovaPoshtaApiException $e) {
    // Помилка API Нової Пошти
    echo "API помилка: " . $e->getMessage();
} catch (AUnhurian\NovaPoshta\SDK\Exceptions\NovaPoshtaHttpException $e) {
    // HTTP помилка (мережева помилка)
    echo "HTTP помилка: " . $e->getMessage();
} catch (Exception $e) {
    // Інші помилки
    echo "Помилка: " . $e->getMessage();
}
```

## Ліцензія

Цей SDK розповсюджується за ліцензією MIT. Детальніше у файлі LICENSE.
