<?php

namespace Tests\Unit\Api;

use Mockery;
use NovaPoshta\SDK\Api\BaseApi;
use NovaPoshta\SDK\Http\NovaPoshtaHttpClient;
use NovaPoshta\SDK\Http\NovaPoshtaResponse;
use Tests\TestCase;

class BaseApiTest extends TestCase
{
    private $httpClientMock;
    private $baseApi;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClientMock = Mockery::mock(NovaPoshtaHttpClient::class);
        
        // Створюємо конкретний екземпляр абстрактного класу через анонімний клас
        $this->baseApi = new class($this->httpClientMock) extends BaseApi {
            protected string $modelName = 'TestModel';
            
            public function callRequest(string $method, array $properties = []): array
            {
                return $this->request($method, $properties);
            }
            
            public function callRequestWithFullResponse(string $method, array $properties = []): NovaPoshtaResponse
            {
                return $this->requestWithFullResponse($method, $properties);
            }
        };
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testRequest(): void
    {
        $mockResponse = [
            'success' => true,
            'data' => [
                [
                    'Ref' => 'item1',
                    'Description' => 'Item 1',
                ],
            ],
        ];

        $this->httpClientMock
            ->shouldReceive('request')
            ->once()
            ->with('TestModel', 'testMethod', ['param' => 'value'])
            ->andReturn($mockResponse);

        $result = $this->baseApi->callRequest('testMethod', ['param' => 'value']);

        $this->assertEquals($mockResponse, $result);
    }

    public function testRequestWithFullResponse(): void
    {
        $mockResponse = Mockery::mock(NovaPoshtaResponse::class);

        $this->httpClientMock
            ->shouldReceive('requestWithFullResponse')
            ->once()
            ->with('TestModel', 'testMethod', ['param' => 'value'])
            ->andReturn($mockResponse);

        $result = $this->baseApi->callRequestWithFullResponse('testMethod', ['param' => 'value']);

        $this->assertSame($mockResponse, $result);
    }
} 