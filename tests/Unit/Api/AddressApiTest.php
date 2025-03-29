<?php

namespace Tests\Unit\Api;

use AUnhurian\NovaPoshta\SDK\Api\AddressApi;
use AUnhurian\NovaPoshta\SDK\Http\NovaPoshtaHttpClient;
use Mockery;
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
                [
                    'Ref' => 'area2',
                    'Description' => 'Львівська',
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

    public function testGetCities(): void
    {
        $mockResponse = [
            'success' => true,
            'data' => [
                [
                    'Ref' => 'city1',
                    'Description' => 'Київ',
                    'Area' => 'area1',
                ],
            ],
        ];

        $params = [
            'FindByString' => 'Київ',
            'Limit' => 20,
        ];

        $this->httpClientMock
            ->shouldReceive('request')
            ->once()
            ->with('Address', 'getCities', $params)
            ->andReturn($mockResponse);

        $result = $this->addressApi->getCities(null, 'Київ', null, 20);

        $this->assertEquals($mockResponse, $result);
    }

    public function testGetWarehouses(): void
    {
        $mockResponse = [
            'success' => true,
            'data' => [
                [
                    'Ref' => 'warehouse1',
                    'Description' => 'Відділення №1',
                    'CityRef' => 'city1',
                ],
            ],
        ];

        $params = [
            'CityRef' => 'city1',
            'FindByString' => 'Відділення',
        ];

        $this->httpClientMock
            ->shouldReceive('request')
            ->once()
            ->with('Address', 'getWarehouses', $params)
            ->andReturn($mockResponse);

        $result = $this->addressApi->getWarehouses('city1', 'Відділення');

        $this->assertEquals($mockResponse, $result);
    }
}
