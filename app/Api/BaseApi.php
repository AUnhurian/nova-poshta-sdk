<?php

declare(strict_types=1);

namespace AUnhurian\NovaPoshta\SDK\Api;

use AUnhurian\NovaPoshta\SDK\Exceptions\NovaPoshtaApiException;
use AUnhurian\NovaPoshta\SDK\Exceptions\NovaPoshtaHttpException;
use AUnhurian\NovaPoshta\SDK\Http\NovaPoshtaHttpClient;
use AUnhurian\NovaPoshta\SDK\Http\NovaPoshtaResponse;

/**
 * Base API module for Nova Poshta API
 */
abstract class BaseApi
{
    /**
     * HTTP client for Nova Poshta API
     *
     * @var NovaPoshtaHttpClient
     */
    protected NovaPoshtaHttpClient $httpClient;

    /**
     * API model name for Nova Poshta API
     *
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
     * Make a request to Nova Poshta API
     *
     * @param string $calledMethod Method name
     * @param array $methodProperties Method properties
     * @return array Response data
     * @throws NovaPoshtaApiException
     * @throws NovaPoshtaHttpException
     */
    protected function request(string $calledMethod, array $methodProperties = []): array
    {
        return $this->httpClient->request($this->modelName, $calledMethod, $methodProperties);
    }

    /**
     * Make a request to Nova Poshta API and get full response object
     *
     * @param string $calledMethod Method name
     * @param array $methodProperties Method properties
     * @return NovaPoshtaResponse Full response object
     * @throws NovaPoshtaApiException
     * @throws NovaPoshtaHttpException
     */
    protected function requestWithFullResponse(string $calledMethod, array $methodProperties = []): NovaPoshtaResponse
    {
        return $this->httpClient->requestWithFullResponse($this->modelName, $calledMethod, $methodProperties);
    }
}
