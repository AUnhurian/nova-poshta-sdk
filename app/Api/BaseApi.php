<?php

namespace NovaPoshta\SDK\Api;

use NovaPoshta\SDK\Http\NovaPoshtaHttpClient;
use NovaPoshta\SDK\Http\NovaPoshtaResponse;

/**
 * Base API class for all Nova Poshta API modules
 */
abstract class BaseApi
{
    /**
     * @var NovaPoshtaHttpClient
     */
    protected NovaPoshtaHttpClient $httpClient;

    /**
     * @var string
     */
    protected string $modelName;

    /**
     * Create a new BaseApi instance
     * 
     * @param NovaPoshtaHttpClient $httpClient
     */
    public function __construct(NovaPoshtaHttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Send a request to Nova Poshta API
     * 
     * @param string $method Method name
     * @param array $properties Method properties
     * @return array Response data
     */
    protected function request(string $method, array $properties = []): array
    {
        return $this->httpClient->request($this->modelName, $method, $properties);
    }
    
    /**
     * Send a request to Nova Poshta API and get full response object
     * 
     * @param string $method Method name
     * @param array $properties Method properties
     * @return NovaPoshtaResponse Full response object
     */
    protected function requestWithFullResponse(string $method, array $properties = []): NovaPoshtaResponse
    {
        return $this->httpClient->requestWithFullResponse($this->modelName, $method, $properties);
    }
}
