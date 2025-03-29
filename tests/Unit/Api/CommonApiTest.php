<?php

namespace Tests\Unit\Api;

use Mockery;
use NovaPoshta\SDK\Api\CommonApi;
use NovaPoshta\SDK\Http\NovaPoshtaHttpClient;
use Tests\TestCase;

class CommonApiTest extends TestCase
{
    private $httpClientMock;
    private CommonApi $commonApi;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClientMock = Mockery::mock(NovaPoshtaHttpClient::class);
        $this->commonApi = new CommonApi($this->httpClientMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testGetCargoTypes(): void
    {
        $mockResponse = [
            [
                'Ref' => 'type1',
                'Description' => 'Вантаж 1',
            ],
            [
                'Ref' => 'type2',
                'Description' => 'Вантаж 2',
            ],
        ];

        $this->httpClientMock
            ->shouldReceive('request')
            ->once()
            ->with('Common', 'getCargoTypes', [])
            ->andReturn($mockResponse);

        $result = $this->commonApi->getCargoTypes();

        $this->assertEquals($mockResponse, $result);
    }

    public function testGetCargoDescriptionList(): void
    {
        $mockResponse = [
            [
                'Ref' => 'desc1',
                'Description' => 'Опис вантажу 1',
            ],
        ];

        $params = [
            'FindByString' => 'Опис',
            'Page' => 1,
            'Limit' => 10,
        ];

        $this->httpClientMock
            ->shouldReceive('request')
            ->once()
            ->with('Common', 'getCargoDescriptionList', $params)
            ->andReturn($mockResponse);

        $result = $this->commonApi->getCargoDescriptionList('Опис', 1, 10);

        $this->assertEquals($mockResponse, $result);
    }

    public function testGetServiceTypes(): void
    {
        $mockResponse = [
            [
                'Ref' => 'service1',
                'Description' => 'Сервіс 1',
            ],
        ];

        $this->httpClientMock
            ->shouldReceive('request')
            ->once()
            ->with('Common', 'getServiceTypes', [])
            ->andReturn($mockResponse);

        $result = $this->commonApi->getServiceTypes();

        $this->assertEquals($mockResponse, $result);
    }

    public function testGetTypesOfPayers(): void
    {
        $mockResponse = [
            [
                'Ref' => 'payer1',
                'Description' => 'Платник 1',
            ],
        ];

        $this->httpClientMock
            ->shouldReceive('request')
            ->once()
            ->with('Common', 'getTypesOfPayers', [])
            ->andReturn($mockResponse);

        $result = $this->commonApi->getTypesOfPayers();

        $this->assertEquals($mockResponse, $result);
    }

    public function testGetTypesOfPayment(): void
    {
        $mockResponse = [
            [
                'Ref' => 'payment1',
                'Description' => 'Спосіб оплати 1',
            ],
        ];

        $this->httpClientMock
            ->shouldReceive('request')
            ->once()
            ->with('Common', 'getTypesOfPayment', [])
            ->andReturn($mockResponse);

        $result = $this->commonApi->getTypesOfPayment();

        $this->assertEquals($mockResponse, $result);
    }

    public function testGetTimeIntervals(): void
    {
        $mockResponse = [
            [
                'Ref' => 'interval1',
                'Description' => '09:00-12:00',
            ],
        ];

        $params = [
            'RecipientCityRef' => 'city1',
            'DateTime' => '01.01.2023',
        ];

        $this->httpClientMock
            ->shouldReceive('request')
            ->once()
            ->with('Common', 'getTimeIntervals', $params)
            ->andReturn($mockResponse);

        $result = $this->commonApi->getTimeIntervals('city1', '01.01.2023');

        $this->assertEquals($mockResponse, $result);
    }

    public function testGetPackList(): void
    {
        $mockResponse = [
            [
                'Ref' => 'pack1',
                'Description' => 'Упаковка 1',
            ],
        ];

        $params = [
            'Length' => '10',
            'Width' => '20',
            'Height' => '30',
        ];

        $this->httpClientMock
            ->shouldReceive('request')
            ->once()
            ->with('Common', 'getPackList', $params)
            ->andReturn($mockResponse);

        $result = $this->commonApi->getPackList('10', '20', '30');

        $this->assertEquals($mockResponse, $result);
    }
} 