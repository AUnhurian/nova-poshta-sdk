<?php

namespace Tests\Unit\Api;

use Mockery;
use NovaPoshta\SDK\Api\TrackingApi;
use NovaPoshta\SDK\Http\NovaPoshtaHttpClient;
use Tests\TestCase;

class TrackingApiTest extends TestCase
{
    private $httpClientMock;
    private TrackingApi $trackingApi;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClientMock = Mockery::mock(NovaPoshtaHttpClient::class);
        $this->trackingApi = new TrackingApi($this->httpClientMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testGetStatusDocuments(): void
    {
        $mockResponse = [
            'success' => true,
            'data' => [
                [
                    'Number' => '20450000000000',
                    'Status' => 'Delivered',
                    'StatusCode' => '9',
                    'ActualDeliveryDate' => '01.01.2023',
                ],
            ],
        ];

        $params = [
            'Documents' => [
                [
                    'DocumentNumber' => '20450000000000',
                ],
            ],
        ];

        $this->httpClientMock
            ->shouldReceive('request')
            ->once()
            ->with('TrackingDocument', 'getStatusDocuments', $params)
            ->andReturn($mockResponse);

        $result = $this->trackingApi->getStatusDocuments('20450000000000');

        $this->assertEquals($mockResponse, $result);
    }

    public function testGetStatusDocumentsWithPhone(): void
    {
        $mockResponse = [
            'success' => true,
            'data' => [
                [
                    'Number' => '20450000000000',
                    'Status' => 'Delivered',
                    'StatusCode' => '9',
                    'ActualDeliveryDate' => '01.01.2023',
                ],
            ],
        ];

        $params = [
            'Documents' => [
                [
                    'DocumentNumber' => '20450000000000',
                    'Phone' => '380991234567',
                ],
            ],
        ];

        $this->httpClientMock
            ->shouldReceive('request')
            ->once()
            ->with('TrackingDocument', 'getStatusDocuments', $params)
            ->andReturn($mockResponse);

        $result = $this->trackingApi->getStatusDocuments('20450000000000', '380991234567');

        $this->assertEquals($mockResponse, $result);
    }

    public function testGetStatusDocumentsBatch(): void
    {
        $mockResponse = [
            'success' => true,
            'data' => [
                [
                    'Number' => '20450000000000',
                    'Status' => 'Delivered',
                    'StatusCode' => '9',
                    'ActualDeliveryDate' => '01.01.2023',
                ],
                [
                    'Number' => '20450000000001',
                    'Status' => 'In progress',
                    'StatusCode' => '4',
                    'ActualDeliveryDate' => '',
                ],
            ],
        ];

        $documents = [
            [
                'DocumentNumber' => '20450000000000',
            ],
            [
                'DocumentNumber' => '20450000000001',
                'Phone' => '380991234567',
            ],
        ];

        $params = [
            'Documents' => $documents,
        ];

        $this->httpClientMock
            ->shouldReceive('request')
            ->once()
            ->with('TrackingDocument', 'getStatusDocuments', $params)
            ->andReturn($mockResponse);

        $result = $this->trackingApi->getStatusDocumentsBatch($documents);

        $this->assertEquals($mockResponse, $result);
    }

    public function testGetStatusHistory(): void
    {
        $mockResponse = [
            'success' => true,
            'data' => [
                [
                    'Number' => '20450000000000',
                    'Date' => '01.01.2023',
                    'StatusCode' => '9',
                    'Status' => 'Delivered',
                ],
                [
                    'Number' => '20450000000000',
                    'Date' => '31.12.2022',
                    'StatusCode' => '4',
                    'Status' => 'In progress',
                ],
            ],
        ];

        $params = [
            'Documents' => [
                [
                    'DocumentNumber' => '20450000000000',
                ],
            ],
        ];

        $this->httpClientMock
            ->shouldReceive('request')
            ->once()
            ->with('TrackingDocument', 'getStatusHistory', $params)
            ->andReturn($mockResponse);

        $result = $this->trackingApi->getStatusHistory('20450000000000');

        $this->assertEquals($mockResponse, $result);
    }
}
