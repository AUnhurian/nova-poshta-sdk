<?php

namespace Tests\Unit;

use AUnhurian\NovaPoshta\SDK\Api\AddressApi;
use AUnhurian\NovaPoshta\SDK\Api\CommonApi;
use AUnhurian\NovaPoshta\SDK\Api\CounterpartyApi;
use AUnhurian\NovaPoshta\SDK\Api\DocumentApi;
use AUnhurian\NovaPoshta\SDK\Api\TrackingApi;
use AUnhurian\NovaPoshta\SDK\Config\NovaPoshtaConfig;
use AUnhurian\NovaPoshta\SDK\Exceptions\NovaPoshtaApiException;
use AUnhurian\NovaPoshta\SDK\Http\NovaPoshtaHttpClient;
use AUnhurian\NovaPoshta\SDK\Http\NovaPoshtaResponse;
use AUnhurian\NovaPoshta\SDK\NovaPoshtaSDK;
use Mockery;
use Tests\TestCase;

class NovaPoshtaSDKTest extends TestCase
{
    private string $apiKey = 'test_api_key';
    private NovaPoshtaSDK $sdk;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sdk = new NovaPoshtaSDK($this->apiKey);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testSDKInstantiation(): void
    {
        $this->assertInstanceOf(NovaPoshtaSDK::class, $this->sdk);
    }

    public function testSDKMethodsReturnCorrectApiInstances(): void
    {
        $this->assertInstanceOf(AddressApi::class, $this->sdk->address());
        $this->assertInstanceOf(CounterpartyApi::class, $this->sdk->counterparty());
        $this->assertInstanceOf(DocumentApi::class, $this->sdk->document());
        $this->assertInstanceOf(TrackingApi::class, $this->sdk->tracking());
        $this->assertInstanceOf(CommonApi::class, $this->sdk->common());
    }

    public function testSDKConfigIsCorrect(): void
    {
        $config = $this->sdk->getConfig();
        $this->assertInstanceOf(NovaPoshtaConfig::class, $config);
        $this->assertEquals($this->apiKey, $config->getApiKey());
        $this->assertEquals('https://api.novaposhta.ua/v2.0/json/', $config->getApiUrl());
    }

    public function testRequest(): void
    {
        $sdk = Mockery::mock(NovaPoshtaSDK::class)->makePartial();
        $httpClientMock = Mockery::mock(NovaPoshtaHttpClient::class);

        $reflection = new \ReflectionProperty(NovaPoshtaSDK::class, 'httpClient');
        $reflection->setAccessible(true);
        $reflection->setValue($sdk, $httpClientMock);

        $mockData = ['data' => ['item1', 'item2']];

        $httpClientMock->shouldReceive('request')
            ->once()
            ->with('Address', 'getAreas', [])
            ->andReturn($mockData);

        $result = $sdk->request('Address', 'getAreas', []);

        $this->assertEquals($mockData, $result);
    }

    public function testRequestWithFullResponse(): void
    {
        $sdk = Mockery::mock(NovaPoshtaSDK::class)->makePartial();
        $httpClientMock = Mockery::mock(NovaPoshtaHttpClient::class);

        $reflection = new \ReflectionProperty(NovaPoshtaSDK::class, 'httpClient');
        $reflection->setAccessible(true);
        $reflection->setValue($sdk, $httpClientMock);

        $mockResponse = Mockery::mock(NovaPoshtaResponse::class);

        $httpClientMock->shouldReceive('requestWithFullResponse')
            ->once()
            ->with('Address', 'getAreas', [])
            ->andReturn($mockResponse);

        $result = $sdk->requestWithFullResponse('Address', 'getAreas', []);

        $this->assertSame($mockResponse, $result);
    }

    /**
     * @test
     */
    public function itCanUseMockResponses(): void
    {
        $sdk = new NovaPoshtaSDK('test_api_key');

        // Налаштування фейкових відповідей
        $mockResponses = [
            'Address.getAreas' => [
                'response' => [
                    'success' => true,
                    'data' => [
                        [
                            'Ref' => 'test-area-ref',
                            'Description' => 'Test Area',
                        ],
                    ],
                    'errors' => [],
                    'warnings' => [],
                    'info' => [],
                ],
                'statusCode' => 200,
            ],
            'Address.getCities' => [
                'params' => [
                    'FindByString' => 'Kyiv',
                ],
                'response' => [
                    'success' => true,
                    'data' => [
                        [
                            'Ref' => 'test-city-ref',
                            'Description' => 'Kyiv',
                        ],
                    ],
                    'errors' => [],
                    'warnings' => [],
                    'info' => [],
                ],
                'statusCode' => 200,
            ],
        ];

        $sdk->setMockResponses($mockResponses);

        // Перевірка getAreas
        $areas = $sdk->request('Address', 'getAreas');
        $this->assertIsArray($areas);
        $this->assertCount(1, $areas);
        $this->assertEquals('test-area-ref', $areas[0]['Ref']);
        $this->assertEquals('Test Area', $areas[0]['Description']);

        // Перевірка getCities з правильним параметром
        $cities = $sdk->request('Address', 'getCities', ['FindByString' => 'Kyiv']);
        $this->assertIsArray($cities);
        $this->assertCount(1, $cities);
        $this->assertEquals('test-city-ref', $cities[0]['Ref']);
        $this->assertEquals('Kyiv', $cities[0]['Description']);

        // Очищення фейкових відповідей
        $sdk->clearMockResponses();
    }

    /**
     * @test
     */
    public function itCanUseMockResponsesWithFullResponse(): void
    {
        $sdk = new NovaPoshtaSDK('test_api_key');

        // Налаштування фейкових відповідей
        $mockResponses = [
            'Address.getAreas' => [
                'response' => [
                    'success' => true,
                    'data' => [
                        [
                            'Ref' => 'test-area-ref',
                            'Description' => 'Test Area',
                        ],
                    ],
                    'errors' => [],
                    'warnings' => [],
                    'info' => [],
                ],
                'statusCode' => 200,
            ],
            'Address.getCities' => [
                'response' => [
                    'success' => false,
                    'data' => [],
                    'errors' => ['Test error message'],
                    'warnings' => [],
                    'info' => [],
                ],
                'statusCode' => 200, // Змінено на 200, щоб перевірити ApiException, а не HttpException
            ],
        ];

        $sdk->setMockResponses($mockResponses);

        // Перевірка getAreas
        $response = $sdk->requestWithFullResponse('Address', 'getAreas');
        $this->assertTrue($response->isSuccess());
        $this->assertEquals(200, $response->getStatusCode());
        $areas = $response->getData();
        $this->assertCount(1, $areas);
        $this->assertEquals('test-area-ref', $areas[0]['Ref']);

        // Перевірка getCities (має викинути виключення)
        $this->expectException(NovaPoshtaApiException::class);
        $sdk->requestWithFullResponse('Address', 'getCities');
    }
}
