<?php

namespace Tests\Unit;

use Mockery;
use NovaPoshta\SDK\NovaPoshtaSDK;
use NovaPoshta\SDK\Api\AddressApi;
use NovaPoshta\SDK\Api\CounterpartyApi;
use NovaPoshta\SDK\Api\DocumentApi;
use NovaPoshta\SDK\Api\TrackingApi;
use NovaPoshta\SDK\Api\CommonApi;
use NovaPoshta\SDK\Config\NovaPoshtaConfig;
use NovaPoshta\SDK\Http\NovaPoshtaHttpClient;
use NovaPoshta\SDK\Http\NovaPoshtaResponse;
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
}
