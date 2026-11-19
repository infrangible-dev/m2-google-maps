<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\GoogleMaps\Model;

use Exception;
use FeWeDev\Base\Json;
use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Stores;
use Infrangible\GoogleMaps\Model\ResourceModel\LocationFactory as LocationResourceFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Client\CurlFactory;
use Psr\Log\LoggerInterface;

/**
 * Searches the location of an address with the Geocoding API of Google Maps; results are cached in the table
 * google_maps_location.
 */
class GeocodingApi
{
    /** URL of the Geocoding API. */
    public const API_URL = 'https://maps.googleapis.com/maps/api/geocode/json';

    /** Seconds to wait for the API. */
    public const TIMEOUT = 10;

    /**
     * @param Variables $variables
     * @param Json $json
     * @param Stores $storeHelper
     * @param LoggerInterface $logging
     * @param LocationFactory $locationFactory
     * @param LocationResourceFactory $locationResourceFactory
     * @param CurlFactory $curlFactory
     */
    public function __construct(
        protected Variables $variables,
        protected Json $json,
        protected Stores $storeHelper,
        protected LoggerInterface $logging,
        protected LocationFactory $locationFactory,
        protected LocationResourceFactory $locationResourceFactory,
        protected CurlFactory $curlFactory
    ) {
    }

    /**
     * Returns the first result of the address, for example with geometry:location:lat and geometry:location:lng.
     *
     * The result is empty if the API found nothing or failed (the error is logged).
     *
     * @param string $address
     * @param array $components filters of the result, for example ['country' => 'DE']
     * @param string $language
     * @phpstan-param array<string, string> $components
     *
     * @return array
     * @phpstan-return array<int|string, mixed>
     */
    public function searchLocation(string $address, array $components = [], string $language = 'en'): array
    {
        $searchParameters = ['key' => $this->getApiKey(), 'address' => $address];

        $componentExpressions = [];

        foreach ($components as $filter => $value) {
            $componentExpressions[] = sprintf('%s:%s', $filter, $value);
        }

        if (count($componentExpressions) > 0) {
            $searchParameters[ 'components' ] = implode('|', $componentExpressions);
        }

        if ($language !== '') {
            $searchParameters[ 'language' ] = $language;
        }

        $searchKey = hash('sha256', (string)$this->json->encode($searchParameters));

        $locationResource = $this->locationResourceFactory->create();
        $location = $this->locationFactory->create();
        $locationResource->load($location, $searchKey, 'key');

        if ($location->getId()) {
            $searchResult = $this->json->decode($this->variables->stringValueOrDefault($location->getData('location')));

            return is_array($searchResult) ? $searchResult : [];
        }

        try {
            $response = $this->request($searchParameters);
        } catch (Exception $exception) {
            $this->logging->error($exception);

            return [];
        }

        if (($response[ 'status' ] ?? null) !== 'OK') {
            $this->logging->error(
                sprintf(
                    'Could not find location of address: %s because: %s',
                    $address,
                    $this->variables->stringValueOrDefault(
                        $response[ 'error_message' ] ?? $response[ 'status' ] ?? null,
                        'unknown error'
                    )
                )
            );

            return [];
        }

        $searchResult = $response[ 'results' ][ 0 ] ?? [];

        if (! is_array($searchResult)) {
            return [];
        }

        $location->setData('key', $searchKey);
        $location->setData('location', $this->json->encode($searchResult));

        try {
            $locationResource->save($location);
        } catch (Exception $exception) {
            $this->logging->error($exception);
        }

        return $searchResult;
    }

    /**
     * Returns the API key: the one of the Geocoding API, or the one of Google Maps.
     *
     * @return string
     */
    protected function getApiKey(): string
    {
        $useMapsKey = $this->storeHelper->getStoreConfigFlag('infrangible_googlemaps/geocoding/use_maps_key', true);

        return trim(
            $this->variables->stringValueOrDefault(
                $this->storeHelper->getStoreConfig(
                    $useMapsKey ? 'infrangible_googlemaps/settings/api_key' : 'infrangible_googlemaps/geocoding/api_key'
                )
            )
        );
    }

    /**
     * Sends the request to the API and returns the decoded response.
     *
     * @param array $searchParameters
     * @phpstan-param array<string, string> $searchParameters
     *
     * @return array
     * @phpstan-return array<int|string, mixed>
     * @throws Exception
     */
    protected function request(array $searchParameters): array
    {
        $client = $this->curlFactory->create();
        $client->setTimeout(self::TIMEOUT);
        $client->addHeader('Referer', $this->storeHelper->getWebUrl());
        $client->get(self::API_URL . '?' . http_build_query($searchParameters));

        if ($client->getStatus() !== 200) {
            throw new LocalizedException(__('The Geocoding API answered with status: %1', $client->getStatus()));
        }

        $response = $this->json->decode($client->getBody());

        return is_array($response) ? $response : [];
    }
}
