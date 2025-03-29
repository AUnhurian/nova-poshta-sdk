# Testing Nova Poshta SDK

This document describes the testing approach and procedures for Nova Poshta SDK.

## Testing Framework

The SDK uses PHPUnit for unit testing. All tests are located in the `tests` directory.

## Running Tests

To run the test suite:

```bash
vendor/bin/phpunit
```

To run a specific test:

```bash
vendor/bin/phpunit --filter TestClassName
```

## Test Structure

The test suite is organized according to the SDK's module structure:

- `tests/Unit/` - Unit tests for individual components
  - `tests/Unit/Api/AddressApiTest.php` - Tests for the AddressApi module
  - `tests/Unit/Api/CounterpartyApiTest.php` - Tests for the CounterpartyApi module
  - `tests/Unit/Api/DocumentApiTest.php` - Tests for the DocumentApi module
  - `tests/Unit/Api/TrackingApiTest.php` - Tests for the TrackingApi module
  - `tests/Unit/Api/CommonApiTest.php` - Tests for the CommonApi module
  - `tests/Unit/NovaPoshtaSDKTest.php` - Tests for the main SDK class
  - `tests/Unit/NovaPoshtaHttpClientTest.php` - Tests for the HTTP client

## Mocking Approach

Tests use the Mockery library to mock HTTP responses from the Nova Poshta API. This approach allows for testing without actual API calls.

Example of mocking HTTP responses:

```php
// Create a mock HTTP client
$httpClient = Mockery::mock(NovaPoshtaHttpClient::class);

// Define the expected request and response
$httpClient->shouldReceive('request')
    ->with('Address', 'getAreas', [])
    ->andReturn([
        'success' => true,
        'data' => [
            ['Ref' => '123', 'Description' => 'Area 1'],
            ['Ref' => '456', 'Description' => 'Area 2'],
        ],
    ]);

// Inject the mock client into the API class
$addressApi = new AddressApi($httpClient);

// Test the method
$result = $addressApi->getAreas();
```

## Writing New Tests

When adding new functionality or fixing bugs, follow these guidelines for writing tests:

1. **Test Method Naming**: Use descriptive method names that explain what is being tested, e.g., `testGetAreasReturnsExpectedData`.

2. **Test Isolation**: Each test should be independent. Do not rely on the state from other tests.

3. **AAA Pattern**: Structure your tests following the Arrange-Act-Assert pattern:
   - Arrange: Set up the test environment and mock dependencies
   - Act: Execute the method being tested
   - Assert: Verify the result matches expectations

4. **Coverage**: Aim for comprehensive test coverage, including:
   - Happy path tests (expected, normal operation)
   - Edge cases (boundary conditions)
   - Error handling (how the code responds to invalid inputs or failures)

5. **Test Readability**: Make tests easy to understand. Use clear variable names and comments where necessary.

Example of a well-structured test:

```php
public function testGetAreasReturnsExpectedData()
{
    // Arrange
    $mockResponse = [
        'success' => true,
        'data' => [
            ['Ref' => '123', 'Description' => 'Area 1'],
            ['Ref' => '456', 'Description' => 'Area 2'],
        ],
    ];
    
    $httpClient = Mockery::mock(NovaPoshtaHttpClient::class);
    $httpClient->shouldReceive('request')
        ->with('Address', 'getAreas', [])
        ->andReturn($mockResponse);
    
    $addressApi = new AddressApi($httpClient);
    
    // Act
    $result = $addressApi->getAreas();
    
    // Assert
    $this->assertCount(2, $result);
    $this->assertEquals('123', $result[0]['Ref']);
    $this->assertEquals('Area 1', $result[0]['Description']);
}
```

## Testing Error Handling

Test how your code handles errors from the API:

```php
public function testHandlesApiError()
{
    // Arrange
    $mockResponse = [
        'success' => false,
        'errors' => ['API error message'],
        'data' => [],
    ];
    
    $httpClient = Mockery::mock(NovaPoshtaHttpClient::class);
    $httpClient->shouldReceive('request')
        ->andReturn($mockResponse);
    
    $addressApi = new AddressApi($httpClient);
    
    // Act & Assert
    $this->expectException(NovaPoshtaApiException::class);
    $addressApi->getAreas();
}
```

## Test Coverage

The project aims for high test coverage. Use PHPUnit's coverage reports to identify untested code:

```bash
vendor/bin/phpunit --coverage-html coverage
```

This will generate an HTML coverage report in the `coverage` directory.

## Continuous Integration

Tests are run automatically in the CI/CD pipeline for every pull request and commit to the main branch. Please ensure all tests pass before submitting your changes. 