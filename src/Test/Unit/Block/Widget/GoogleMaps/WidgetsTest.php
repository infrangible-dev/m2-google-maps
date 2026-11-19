<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\GoogleMaps\Test\Unit\Block\Widget\GoogleMaps;

use FeWeDev\Base\Arrays;
use FeWeDev\Base\Json;
use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Registry;
use Infrangible\Core\Helper\Stores;
use Infrangible\GoogleMaps\Block\Widget\GoogleMaps\Api;
use Infrangible\GoogleMaps\Block\Widget\GoogleMaps\Autocomplete;
use Infrangible\GoogleMaps\Block\Widget\GoogleMaps\JsonMarker;
use Infrangible\GoogleMaps\Block\Widget\GoogleMaps\SimpleMarker;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Template\Context;
use PHPUnit\Framework\TestCase;

/**
 * Tests the values of the widgets that the templates write into the page.
 */
class WidgetsTest extends TestCase
{
    /**
     * Returns the context of the blocks, with the request parameters.
     *
     * @param array<string, mixed> $parameters
     *
     * @return Context
     */
    private function createContext(array $parameters = []): Context
    {
        $request = $this->createStub(RequestInterface::class);
        $request->method('getParam')->willReturnCallback(fn(string $key): mixed => $parameters[ $key ] ?? null);

        $context = $this->createStub(Context::class);
        $context->method('getRequest')->willReturn($request);

        return $context;
    }

    /**
     * Returns a widget with markers of the JSON.
     *
     * @param string $markerJson
     *
     * @return JsonMarker
     */
    private function createJsonMarker(string $markerJson): JsonMarker
    {
        $variables = new Variables();

        return new JsonMarker(
            $this->createContext(),
            $this->createStub(Registry::class),
            new Json(),
            new Arrays($variables),
            $variables,
            ['marker_json' => $markerJson]
        );
    }

    /**
     * Tests the sizes: pixels or CSS lengths, other values are ignored, so they cannot add other CSS.
     *
     * @return void
     */
    public function testSizeStyles(): void
    {
        $marker = new SimpleMarker(
            $this->createContext(),
            $this->createStub(Registry::class),
            ['width' => '400', 'height' => '50vh']
        );

        $this->assertSame('width: 400px;', $marker->getWidthStyle());
        $this->assertSame('height: 50vh;', $marker->getHeightStyle());

        $marker->setData('width', '100%; background: url(x)');
        $marker->unsetData('height');

        $this->assertNull($marker->getWidthStyle());
        $this->assertNull($marker->getHeightStyle());
    }

    /**
     * Tests the center of the markers on the sphere, and that markers without coordinates no longer divide by zero.
     *
     * @return void
     */
    public function testMapCenter(): void
    {
        [$latitude, $longitude] = $this->createJsonMarker(
            '[<<<"lat":50,"lng":10>>>,<<<"lat":52,"lng":12>>>]'
        )->getMapCenter();

        $this->assertEqualsWithDelta(51.0, $latitude, 0.05);
        $this->assertEqualsWithDelta(11.0, $longitude, 0.05);
        $this->assertSame([0.0, 0.0], $this->createJsonMarker('[{"title":"no coordinates"}]')->getMapCenter());
        $this->assertSame([0.0, 0.0], $this->createJsonMarker('not json')->getMapCenter());
    }

    /**
     * Tests that the markers are JSON that cannot end the script element or a string of the script; before, a quote in
     * the info window ended the string of JSON.parse().
     *
     * @return void
     */
    public function testLocationsJson(): void
    {
        $json = $this->createJsonMarker(
            '[{"lat":50,"lng":10,"title":"O\'Neill","info_window":"<p>Shop</p></script>"}]'
        )->getLocationsJson();

        $this->assertStringNotContainsString('</script>', $json);
        $this->assertStringNotContainsString("'", $json);
        $this->assertSame(
            [['lat' => 50, 'lng' => 10, 'title' => "O'Neill", 'info_window' => '<p>Shop</p></script>']],
            json_decode($json, true)
        );
    }

    /**
     * Tests the value of the autocomplete input of the request, which the template escapes.
     *
     * @return void
     */
    public function testAutocompleteValue(): void
    {
        $autocomplete = new Autocomplete(
            $this->createContext(['location' => 'Jena', 'list' => ['a']]),
            $this->createStub(Registry::class),
            ['input_id' => 'location']
        );

        $this->assertSame('Jena', $autocomplete->getInputValue());

        $autocomplete->setData('input_id', 'list');

        $this->assertNull($autocomplete->getInputValue());
    }

    /**
     * Tests the URL of the script of the API with the encoded key and the language of the store.
     *
     * @return void
     */
    public function testScriptUrl(): void
    {
        $values = ['infrangible_googlemaps/settings/api_key' => ' key&x=1 ', 'general/locale/code' => 'de_DE'];

        $storeHelper = $this->createStub(Stores::class);
        $storeHelper->method('getStoreConfig')->willReturnCallback(
            fn(string $path): ?string => $values[ $path ] ?? null
        );

        $api = new Api($this->createContext(), $this->createStub(Registry::class), $storeHelper, new Variables());

        $this->assertSame(
            'https://maps.googleapis.com/maps/api/js?key=key%26x%3D1&language=de&libraries=places' .
            '&callback=googleMapsApi&v=weekly',
            $api->getScriptUrl()
        );
    }
}
