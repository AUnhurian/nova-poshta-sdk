<?php

namespace Tests\Unit\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Mockery;
use NovaPoshta\SDK\Config\NovaPoshtaConfig;
use NovaPoshta\SDK\Exceptions\NovaPoshtaApiException;
use NovaPoshta\SDK\Exceptions\NovaPoshtaHttpException;
use NovaPoshta\SDK\Http\NovaPoshtaHttpClient;
use Tests\TestCase;
use ReflectionClass;

class NovaPoshtaHttpClientTest extends TestCase
{
    private NovaPoshtaConfig $config;
    private NovaPoshtaHttpClient $httpClient;

    protected function setUp(): void
    {
        parent::setUp();
        $this->config = new NovaPoshtaConfig('test_api_key');
        $this->httpClient = new NovaPoshtaHttpClient($this->config);
    }

    protected function mockHttpClient(NovaPoshtaHttpClient $httpClient, Response $response): void
    {
        $mock = new MockHandler([$response]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $reflectionClass = new ReflectionClass(NovaPoshtaHttpClient::class);
        $property = $reflectionClass->getProperty('httpClient');
        $property->setAccessible(true);
        $property->setValue($httpClient, $client);
    }

    public function testSuccessfulRequest(): void
    {
        $responseData = [
            'success' => true,
            'data' => [['key' => 'value']],
            'errors' => [],
            'warnings' => [],
            'info' => [],
        ];
        
        $successResponse = new Response(200, [], json_encode($responseData));

        $this->mockHttpClient($this->httpClient, $successResponse);
        
        $result = $this->httpClient->request('TestModel', 'TestMethod', ['param' => 'value']);
        
        $this->assertEquals($responseData['data'], $result);
    }

    public function testApiErrorResponse(): void
    {
        $errorResponse = new Response(200, [], json_encode([
            'success' => false,
            'data' => [],
            'errors' => ['API Error'],
            'warnings' => [],
            'info' => [],
        ]));

        $this->mockHttpClient($this->httpClient, $errorResponse);
        
        $this->expectException(NovaPoshtaApiException::class);
        $this->httpClient->request('TestModel', 'TestMethod', ['param' => 'value']);
    }

    public function testHttpErrorResponse(): void
    {
        $httpErrorResponse = new Response(500, [], 'Internal Server Error');

        $this->mockHttpClient($this->httpClient, $httpErrorResponse);
        
        $this->expectException(NovaPoshtaHttpException::class);
        $this->httpClient->request('TestModel', 'TestMethod', ['param' => 'value']);
    }

    public function testInvalidJsonResponse(): void
    {
        $invalidJsonResponse = new Response(200, [], '{invalid_json:');

        $this->mockHttpClient($this->httpClient, $invalidJsonResponse);
        
        $this->expectException(NovaPoshtaHttpException::class);
        $this->httpClient->request('TestModel', 'TestMethod', ['param' => 'value']);
    }
}
