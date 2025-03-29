<?php

declare(strict_types=1);

namespace AUnhurian\NovaPoshta\SDK;

use AUnhurian\NovaPoshta\SDK\Api\AddressApi;
use AUnhurian\NovaPoshta\SDK\Api\CommonApi;
use AUnhurian\NovaPoshta\SDK\Api\CounterpartyApi;
use AUnhurian\NovaPoshta\SDK\Api\DocumentApi;
use AUnhurian\NovaPoshta\SDK\Api\TrackingApi;
use AUnhurian\NovaPoshta\SDK\Config\NovaPoshtaConfig;
use AUnhurian\NovaPoshta\SDK\Exceptions\NovaPoshtaApiException;
use AUnhurian\NovaPoshta\SDK\Exceptions\NovaPoshtaHttpException;
use AUnhurian\NovaPoshta\SDK\Http\NovaPoshtaHttpClient;
use AUnhurian\NovaPoshta\SDK\Http\NovaPoshtaResponse;

/**
 * Main SDK class for Nova Poshta API integration
 */
class NovaPoshtaSDK
{
    /**
     * @var NovaPoshtaConfig
     */
    private NovaPoshtaConfig $config;

    /**
     * @var NovaPoshtaHttpClient
     */
    private NovaPoshtaHttpClient $httpClient;

    /**
     * @var AddressApi
     */
    private AddressApi $addressApi;

    /**
     * @var CounterpartyApi
     */
    private CounterpartyApi $counterpartyApi;

    /**
     * @var DocumentApi
     */
    private DocumentApi $documentApi;

    /**
     * @var TrackingApi
     */
    private TrackingApi $trackingApi;

    /**
     * @var CommonApi
     */
    private CommonApi $commonApi;

    /**
     * Create a new NovaPoshtaSDK instance
     *
     * @param string $apiKey API key from Nova Poshta
     */
    public function __construct(string $apiKey)
    {
        $this->config = new NovaPoshtaConfig($apiKey);
        $this->httpClient = new NovaPoshtaHttpClient($this->config);

        // Initialize API modules
        $this->addressApi = new AddressApi($this->httpClient);
        $this->counterpartyApi = new CounterpartyApi($this->httpClient);
        $this->documentApi = new DocumentApi($this->httpClient);
        $this->trackingApi = new TrackingApi($this->httpClient);
        $this->commonApi = new CommonApi($this->httpClient);
    }

    /**
     * Get the Address API module
     *
     * @return AddressApi
     */
    public function address(): AddressApi
    {
        return $this->addressApi;
    }

    /**
     * Get the Counterparty API module
     *
     * @return CounterpartyApi
     */
    public function counterparty(): CounterpartyApi
    {
        return $this->counterpartyApi;
    }

    /**
     * Get the Document API module
     *
     * @return DocumentApi
     */
    public function document(): DocumentApi
    {
        return $this->documentApi;
    }

    /**
     * Get the Tracking API module
     *
     * @return TrackingApi
     */
    public function tracking(): TrackingApi
    {
        return $this->trackingApi;
    }

    /**
     * Get the Common API module (reference data)
     *
     * @return CommonApi
     */
    public function common(): CommonApi
    {
        return $this->commonApi;
    }

    /**
     * Get the SDK configuration
     *
     * @return NovaPoshtaConfig
     */
    public function getConfig(): NovaPoshtaConfig
    {
        return $this->config;
    }

    /**
     * Make a direct API request with a specific model and method
     *
     * @param string $modelName API model name
     * @param string $calledMethod API method name
     * @param array $methodProperties Method properties
     * @return array Response data
     * @throws NovaPoshtaApiException
     * @throws NovaPoshtaHttpException
     */
    public function request(string $modelName, string $calledMethod, array $methodProperties = []): array
    {
        return $this->httpClient->request($modelName, $calledMethod, $methodProperties);
    }

    /**
     * Make a direct API request with a specific model and method and get full response object
     *
     * @param string $modelName API model name
     * @param string $calledMethod API method name
     * @param array $methodProperties Method properties
     * @return NovaPoshtaResponse Full response object
     * @throws NovaPoshtaApiException
     * @throws NovaPoshtaHttpException
     */
    public function requestWithFullResponse(string $modelName, string $calledMethod, array $methodProperties = []): NovaPoshtaResponse
    {
        return $this->httpClient->requestWithFullResponse($modelName, $calledMethod, $methodProperties);
    }
}
