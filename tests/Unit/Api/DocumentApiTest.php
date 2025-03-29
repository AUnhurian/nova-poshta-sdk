<?php

namespace Tests\Unit\Api;

use AUnhurian\NovaPoshta\SDK\Api\DocumentApi;
use AUnhurian\NovaPoshta\SDK\Http\NovaPoshtaHttpClient;
use Mockery;
use Tests\TestCase;

class DocumentApiTest extends TestCase
{
    private $httpClientMock;
    private DocumentApi $documentApi;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClientMock = Mockery::mock(NovaPoshtaHttpClient::class);
        $this->documentApi = new DocumentApi($this->httpClientMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testSave(): void
    {
        $mockResponse = [
            'success' => true,
            'data' => [
                [
                    'Ref' => 'doc1',
                    'CostOnSite' => 50,
                    'EstimatedDeliveryDate' => '01.01.2023',
                    'IntDocNumber' => '20450000000000',
                ],
            ],
        ];

        $params = [
            'PayerType' => 'Sender',
            'PaymentMethod' => 'Cash',
            'CargoType' => 'Cargo',
            'Weight' => 1,
            'ServiceType' => 'WarehouseWarehouse',
            'SeatsAmount' => 1,
            'Description' => 'Опис вантажу',
            'Cost' => 500,
            'CitySender' => 'city1',
            'Sender' => 'sender1',
            'SenderAddress' => 'address1',
            'ContactSender' => 'contact1',
            'SendersPhone' => '380991234567',
            'CityRecipient' => 'city2',
            'Recipient' => 'recipient1',
            'RecipientAddress' => 'address2',
            'ContactRecipient' => 'contact2',
            'RecipientsPhone' => '380991234567',
        ];

        $this->httpClientMock
            ->shouldReceive('request')
            ->once()
            ->with('InternetDocument', 'save', $params)
            ->andReturn($mockResponse);

        $result = $this->documentApi->save($params);

        $this->assertEquals($mockResponse, $result);
    }

    public function testDelete(): void
    {
        $mockResponse = [
            'success' => true,
            'data' => [
                [
                    'Ref' => 'doc1',
                ],
            ],
        ];

        $params = [
            'DocumentRefs' => 'doc1',
        ];

        $this->httpClientMock
            ->shouldReceive('request')
            ->once()
            ->with('InternetDocument', 'delete', $params)
            ->andReturn($mockResponse);

        $result = $this->documentApi->delete('doc1');

        $this->assertEquals($mockResponse, $result);
    }

    public function testGetDocumentPrice(): void
    {
        $mockResponse = [
            'success' => true,
            'data' => [
                [
                    'Cost' => 50,
                    'CostRedelivery' => 500,
                    'AssessedCost' => 500,
                ],
            ],
        ];

        $params = [
            'CitySender' => 'city1',
            'CityRecipient' => 'city2',
            'Weight' => '1',
            'Cost' => 500,
            'ServiceType' => 'WarehouseWarehouse',
            'CargoType' => 1,
            'SeatsAmount' => 1,
        ];

        $this->httpClientMock
            ->shouldReceive('request')
            ->once()
            ->with('InternetDocument', 'getDocumentPrice', $params)
            ->andReturn($mockResponse);

        $result = $this->documentApi->getDocumentPrice(
            'city1',
            'city2',
            '1',
            500,
            'WarehouseWarehouse',
            1,
            1
        );

        $this->assertEquals($mockResponse, $result);
    }

    public function testGetDocumentPriceWithPackage(): void
    {
        $mockResponse = [
            'success' => true,
            'data' => [
                [
                    'Cost' => 60,
                    'CostRedelivery' => 500,
                    'AssessedCost' => 500,
                ],
            ],
        ];

        $params = [
            'CitySender' => 'city1',
            'CityRecipient' => 'city2',
            'Weight' => '1',
            'Cost' => 500,
            'ServiceType' => 'WarehouseWarehouse',
            'CargoType' => 1,
            'SeatsAmount' => 1,
            'PackCalculate' => [
                'PackRef' => 'pack1',
            ],
        ];

        $this->httpClientMock
            ->shouldReceive('request')
            ->once()
            ->with('InternetDocument', 'getDocumentPrice', $params)
            ->andReturn($mockResponse);

        $result = $this->documentApi->getDocumentPrice(
            'city1',
            'city2',
            '1',
            500,
            'WarehouseWarehouse',
            1,
            1,
            'pack1'
        );

        $this->assertEquals($mockResponse, $result);
    }

    public function testGetDocumentDeliveryDate(): void
    {
        $mockResponse = [
            'success' => true,
            'data' => [
                [
                    'DeliveryDate' => '01.01.2023',
                ],
            ],
        ];

        $params = [
            'CitySender' => 'city1',
            'CityRecipient' => 'city2',
            'ServiceType' => 'WarehouseWarehouse',
            'DateTime' => '01.01.2023',
        ];

        $this->httpClientMock
            ->shouldReceive('request')
            ->once()
            ->with('InternetDocument', 'getDocumentDeliveryDate', $params)
            ->andReturn($mockResponse);

        $result = $this->documentApi->getDocumentDeliveryDate(
            'city1',
            'city2',
            'WarehouseWarehouse',
            '01.01.2023'
        );

        $this->assertEquals($mockResponse, $result);
    }

    public function testGetDocumentList(): void
    {
        $mockResponse = [
            'success' => true,
            'data' => [
                [
                    'Ref' => 'doc1',
                    'DateTime' => '01.01.2023',
                    'IntDocNumber' => '20450000000000',
                ],
            ],
        ];

        $params = [
            'DateTimeFrom' => '01.01.2023',
            'DateTimeTo' => '31.01.2023',
            'Page' => '1',
            'Limit' => '10',
        ];

        $this->httpClientMock
            ->shouldReceive('request')
            ->once()
            ->with('InternetDocument', 'getDocumentList', $params)
            ->andReturn($mockResponse);

        $result = $this->documentApi->getDocumentList(
            '01.01.2023',
            '31.01.2023',
            '1',
            '10'
        );

        $this->assertEquals($mockResponse, $result);
    }

    public function testGetDocument(): void
    {
        $mockResponse = [
            'success' => true,
            'data' => [
                [
                    'Ref' => 'doc1',
                    'IntDocNumber' => '20450000000000',
                    'Cost' => 50,
                ],
            ],
        ];

        $params = [
            'Ref' => 'doc1',
        ];

        $this->httpClientMock
            ->shouldReceive('request')
            ->once()
            ->with('InternetDocument', 'getDocument', $params)
            ->andReturn($mockResponse);

        $result = $this->documentApi->getDocument('doc1');

        $this->assertEquals($mockResponse, $result);
    }

    public function testGenerateReport(): void
    {
        $mockResponse = [
            'success' => true,
            'data' => [
                [
                    'Ref' => 'doc1',
                    'Type' => 'pdf',
                ],
            ],
        ];

        $params = [
            'DocumentRefs' => 'doc1',
            'Type' => 'pdf',
        ];

        $this->httpClientMock
            ->shouldReceive('request')
            ->once()
            ->with('InternetDocument', 'generateReport', $params)
            ->andReturn($mockResponse);

        $result = $this->documentApi->generateReport('doc1', 'pdf');

        $this->assertEquals($mockResponse, $result);
    }
}
