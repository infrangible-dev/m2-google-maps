<?php

declare(strict_types=1);

namespace Infrangible\GoogleMaps\Model;

use Exception;
use FeWeDev\Base\Arrays;
use FeWeDev\Base\Json;
use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Stores;
use PestJSON;
use Psr\Log\LoggerInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class GeocodingApi
{
    /** @var Variables */
    protected $variables;

    /** @var Arrays */
    protected $arrays;

    /** @var Json */
    protected $json;

    /** @var Stores */
    protected $storeHelper;

    /** @var LoggerInterface */
    protected $logging;

    /** @var LocationFactory */
    protected $locationFactory;

    /** @var \Infrangible\GoogleMaps\Model\ResourceModel\LocationFactory */
    protected $locationResourceFactory;

    /**
     * @param Variables                                                   $variables
     * @param Arrays                                                      $arrays
     * @param Json                                                        $json
     * @param Stores                                                      $storeHelper
     * @param LoggerInterface                                             $logging
     * @param LocationFactory                                             $locationFactory
     * @param \Infrangible\GoogleMaps\Model\ResourceModel\LocationFactory $locationResourceFactory
     */
    public function __construct(
        Variables $variables,
        Arrays $arrays,
        Json $json,
        Stores $storeHelper,
        LoggerInterface $logging,
        LocationFactory $locationFactory,
        \Infrangible\GoogleMaps\Model\ResourceModel\LocationFactory $locationResourceFactory
    ) {
        $this->variables = $variables;
        $this->arrays = $arrays;
        $this->json = $json;
        $this->storeHelper = $storeHelper;

        $this->logging = $logging;
        $this->locationFactory = $locationFactory;
        $this->locationResourceFactory = $locationResourceFactory;
    }

    /**
     * @param string $address
     * @param array  $components
     * @param string $language
     *
     * @return array
     */
    public function searchLocation(string $address, array $components = [], string $language = 'en'): array
    {
        $mapsUrl = 'https://maps.googleapis.com';
        $apiUrl = 'maps/api/geocode/json';

        $useMapsKey = $this->storeHelper->getStoreConfig('infrangible_googlemaps/geocoding/use_maps_key', true, true);
        if ($useMapsKey) {
            $apiKey = $this->storeHelper->getStoreConfig('infrangible_googlemaps/settings/api_key');
        } else {
            $apiKey = $this->storeHelper->getStoreConfig('infrangible_googlemaps/geocoding/api_key');
        }


        $searchParameters = ['key' => $apiKey, 'address' => $address];

        if (!$this->variables->isEmpty($components)) {
            $componentExpressions = [];

            foreach ($components as $key => $value) {
                $componentExpressions[] = sprintf('%s:%s', $key, $value);
            }

            if (!$this->variables->isEmpty($componentExpressions)) {
                $searchParameters['components'] = implode('|', $componentExpressions);
            }
        }

        if (!$this->variables->isEmpty($language)) {
            $searchParameters['language'] = $language;
        }

        $searchKey = md5($this->json->encode($searchParameters));

        $locationFactory = $this->locationResourceFactory->create();

        $location = $this->locationFactory->create();

        $locationFactory->load($location, $searchKey, 'key');

        if ($location->getId()) {
            $searchResult = $this->json->decode($location->getLocation());
        } else {
            $searchResult = [];

            try {
                $client = new PestJSON($mapsUrl);

                $client->curl_opts[CURLOPT_REFERER] = $this->storeHelper->getWebUrl();

                $response = $client->get(
                    $apiUrl,
                    $searchParameters,
                    ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (HTML5, like Gecko) Chrome/103.0.0.0 Safari/537.36']
                );

                if (is_array($response)) {
                    $status = $this->arrays->getValue($response, 'status');

                    if ($status === 'OK') {
                        $searchResult = $this->arrays->getValue($response, 'results:0', []);

                        $location->setKey($searchKey);
                        $location->setLocation($this->json->encode($searchResult));

                        $locationFactory->save($location);
                    } else {
                        $this->logging->error(
                            $this->arrays->getValue(
                                $response,
                                'error_message',
                                sprintf('Could not check location for location: %s', $address)
                            )
                        );
                    }
                }
            } catch (Exception $exception) {
                $this->logging->error($exception);
            }
        }

        return $searchResult;
    }
}
