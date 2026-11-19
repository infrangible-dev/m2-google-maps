<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\GoogleMaps\Test\Unit\Model;

use FeWeDev\Base\Json;
use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Stores;
use Infrangible\GoogleMaps\Model\GeocodingApi;
use Infrangible\GoogleMaps\Model\Location;
use Infrangible\GoogleMaps\Model\LocationFactory;
use Infrangible\GoogleMaps\Model\ResourceModel\Location as LocationResource;
use Infrangible\GoogleMaps\Model\ResourceModel\LocationFactory as LocationResourceFactory;
use Magento\Framework\DataObject;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\HTTP\Client\CurlFactory;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

/**
 * Tests the search of locations: the request, the cache of results and errors of the API.
 */
class GeocodingApiTest extends TestCase
{
    /** @var string[] requested URLs */
    private array $requests = [];

    /** @var DataObject|null the saved location */
    private ?DataObject $saved = null;

    /**
     * Returns the API with a cached location (or none) and the response of the API.
     *
     * @param string|null $cachedLocation
     * @param int $status
     * @param string $body
     *
     * @return GeocodingApi
     */
    private function createApi(?string $cachedLocation, int $status = 200, string $body = ''): GeocodingApi
    {
        $values = ['infrangible_googlemaps/settings/api_key' => 'secret'];

        $storeHelper = $this->createStub(Stores::class);
        $storeHelper->method('getStoreConfig')->willReturnCallback(
            fn(string $path): ?string => $values[ $path ] ?? null
        );
        $storeHelper->method('getStoreConfigFlag')->willReturn(true);
        $storeHelper->method('getWebUrl')->willReturn('https://shop.test/');

        $location = $this->createStub(Location::class);
        $location->method('getId')->willReturn($cachedLocation === null ? null : '1');
        $location->method('getData')->willReturn($cachedLocation);

        $locationFactory = $this->createStub(LocationFactory::class);
        $locationFactory->method('create')->willReturn($location);

        $locationResource = $this->createStub(LocationResource::class);
        $locationResource->method('save')->willReturnCallback(
            function (DataObject $object) use ($locationResource): LocationResource {
                $this->saved = $object;

                return $locationResource;
            }
        );

        $locationResourceFactory = $this->createStub(LocationResourceFactory::class);
        $locationResourceFactory->method('create')->willReturn($locationResource);

        $client = $this->createStub(Curl::class);
        $client->method('get')->willReturnCallback(function (string $url): void {
            $this->requests[] = $url;
        });
        $client->method('getStatus')->willReturn($status);
        $client->method('getBody')->willReturn($body);

        $curlFactory = $this->createStub(CurlFactory::class);
        $curlFactory->method('create')->willReturn($client);

        return new GeocodingApi(
            new Variables(),
            new Json(),
            $storeHelper,
            new NullLogger(),
            $locationFactory,
            $locationResourceFactory,
            $curlFactory
        );
    }

    /**
     * Tests the request to the API and that the first result is saved.
     *
     * @return void
     */
    public function testRequest(): void
    {
        $result = $this->createApi(
            null,
            200,
            '{"status":"OK","results":[{"geometry":{"location":{"lat":50.9,"lng":11.6}}}]}'
        )->searchLocation('Brückenstr. 16, Jena', ['country' => 'DE'], 'de');

        $this->assertSame(['geometry' => ['location' => ['lat' => 50.9, 'lng' => 11.6]]], $result);
        $this->assertSame(
            ['https://maps.googleapis.com/maps/api/geocode/json?key=secret&address=Br%C3%BCckenstr.+16%2C+Jena' .
             '&components=country%3ADE&language=de'],
            $this->requests
        );
        $this->assertNotNull($this->saved);
    }

    /**
     * Tests that a cached result is used without request.
     *
     * @return void
     */
    public function testCachedResult(): void
    {
        $result = $this->createApi('{"place_id":"abc"}')->searchLocation('Jena');

        $this->assertSame(['place_id' => 'abc'], $result);
        $this->assertSame([], $this->requests);
    }

    /**
     * Tests that errors of the API return no result and are not saved.
     *
     * @return void
     */
    public function testErrors(): void
    {
        $this->assertSame(
            [],
            $this->createApi(null, 200, '{"status":"REQUEST_DENIED","error_message":"Invalid key"}')
                ->searchLocation('Jena')
        );
        $this->assertSame([], $this->createApi(null, 500)->searchLocation('Jena'));
        $this->assertNull($this->saved);
    }
}
