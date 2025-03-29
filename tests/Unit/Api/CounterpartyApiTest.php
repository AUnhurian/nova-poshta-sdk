<?php

namespace Tests\Unit\Api;

use AUnhurian\NovaPoshta\SDK\Api\CounterpartyApi;
use AUnhurian\NovaPoshta\SDK\Http\NovaPoshtaHttpClient;
use Mockery;
use Tests\TestCase;

class CounterpartyApiTest extends TestCase
{
    private $httpClientMock;
    private CounterpartyApi $counterpartyApi;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClientMock = Mockery::mock(NovaPoshtaHttpClient::class);
        $this->counterpartyApi = new CounterpartyApi($this->httpClientMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testSavePrivatePerson(): void
    {
        $mockResponse = [
            'success' => true,
            'data' => [
                [
                    'Ref' => 'person1',
                    'Description' => 'Петренко Іван Васильович',
                    'CounterpartyType' => 'PrivatePerson',
                ],
            ],
        ];

        $params = [
            'CounterpartyType' => 'PrivatePerson',
            'CounterpartyProperty' => 'Recipient',
            'FirstName' => 'Іван',
            'LastName' => 'Петренко',
            'MiddleName' => 'Васильович',
            'Phone' => '380991234567',
            'Email' => 'test@example.com',
        ];

        $this->httpClientMock
            ->shouldReceive('request')
            ->once()
            ->with('Counterparty', 'save', $params)
            ->andReturn($mockResponse);

        $result = $this->counterpartyApi->save(
            'PrivatePerson',
            'Іван',
            'Петренко',
            'Васильович',
            '380991234567',
            'test@example.com'
        );

        $this->assertEquals($mockResponse, $result);
    }

    public function testSaveOrganization(): void
    {
        $mockResponse = [
            'success' => true,
            'data' => [
                [
                    'Ref' => 'org1',
                    'Description' => 'ТОВ "Компанія"',
                    'CounterpartyType' => 'Organization',
                ],
            ],
        ];

        $params = [
            'CounterpartyType' => 'Organization',
            'CounterpartyProperty' => 'Recipient',
            'CompanyName' => 'ТОВ "Компанія"',
            'EDRPOU' => '12345678',
        ];

        $this->httpClientMock
            ->shouldReceive('request')
            ->once()
            ->with('Counterparty', 'save', $params)
            ->andReturn($mockResponse);

        $result = $this->counterpartyApi->save(
            'Organization',
            null,
            null,
            null,
            null,
            null,
            'ТОВ "Компанія"',
            '12345678'
        );

        $this->assertEquals($mockResponse, $result);
    }

    public function testUpdate(): void
    {
        $mockResponse = [
            'success' => true,
            'data' => [
                [
                    'Ref' => 'person1',
                    'Description' => 'Петренко Іван Васильович',
                    'CounterpartyType' => 'PrivatePerson',
                ],
            ],
        ];

        $params = [
            'Ref' => 'person1',
            'CounterpartyType' => 'PrivatePerson',
            'CounterpartyProperty' => 'Recipient',
            'Phone' => '380991234567',
            'Email' => 'new@example.com',
        ];

        $this->httpClientMock
            ->shouldReceive('request')
            ->once()
            ->with('Counterparty', 'update', $params)
            ->andReturn($mockResponse);

        $result = $this->counterpartyApi->update(
            'person1',
            'PrivatePerson',
            null,
            null,
            null,
            '380991234567',
            'new@example.com'
        );

        $this->assertEquals($mockResponse, $result);
    }

    public function testDelete(): void
    {
        $mockResponse = [
            'success' => true,
            'data' => [
                [
                    'Ref' => 'person1',
                ],
            ],
        ];

        $params = [
            'Ref' => 'person1',
        ];

        $this->httpClientMock
            ->shouldReceive('request')
            ->once()
            ->with('Counterparty', 'delete', $params)
            ->andReturn($mockResponse);

        $result = $this->counterpartyApi->delete('person1');

        $this->assertEquals($mockResponse, $result);
    }

    public function testGetCounterpartyAddresses(): void
    {
        $mockResponse = [
            'success' => true,
            'data' => [
                [
                    'Ref' => 'address1',
                    'Description' => 'Вулиця Хрещатик, 1, Київ',
                    'CounterpartyRef' => 'person1',
                ],
            ],
        ];

        $params = [
            'Ref' => 'person1',
            'CounterpartyProperty' => 'Recipient',
        ];

        $this->httpClientMock
            ->shouldReceive('request')
            ->once()
            ->with('Counterparty', 'getCounterpartyAddresses', $params)
            ->andReturn($mockResponse);

        $result = $this->counterpartyApi->getCounterpartyAddresses('person1');

        $this->assertEquals($mockResponse, $result);
    }

    public function testGetCounterpartyContactPersons(): void
    {
        $mockResponse = [
            'success' => true,
            'data' => [
                [
                    'Ref' => 'contact1',
                    'Description' => 'Петренко Іван Васильович',
                    'CounterpartyRef' => 'person1',
                ],
            ],
        ];

        $params = [
            'Ref' => 'person1',
            'CounterpartyProperty' => 'Sender',
        ];

        $this->httpClientMock
            ->shouldReceive('request')
            ->once()
            ->with('Counterparty', 'getCounterpartyContactPersons', $params)
            ->andReturn($mockResponse);

        $result = $this->counterpartyApi->getCounterpartyContactPersons('person1', 'Sender');

        $this->assertEquals($mockResponse, $result);
    }

    public function testGetCounterparties(): void
    {
        $mockResponse = [
            'success' => true,
            'data' => [
                [
                    'Ref' => 'person1',
                    'Description' => 'Петренко Іван Васильович',
                    'CounterpartyType' => 'PrivatePerson',
                ],
            ],
        ];

        $params = [
            'FindByString' => 'Петренко',
            'CounterpartyProperty' => 'Recipient',
            'Page' => 1,
            'Limit' => 10,
        ];

        $this->httpClientMock
            ->shouldReceive('request')
            ->once()
            ->with('Counterparty', 'getCounterparties', $params)
            ->andReturn($mockResponse);

        $result = $this->counterpartyApi->getCounterparties('Петренко', 'Recipient', 1, 10);

        $this->assertEquals($mockResponse, $result);
    }
}
