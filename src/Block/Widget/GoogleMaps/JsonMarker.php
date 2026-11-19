<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\GoogleMaps\Block\Widget\GoogleMaps;

use FeWeDev\Base\Arrays;
use FeWeDev\Base\Json;
use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Registry;
use Magento\Framework\View\Element\Template;

/**
 * Widget with a map and the markers of a JSON list: latitude (lat), longitude (lng), title and info window
 * (info_window) of every marker.
 */
class JsonMarker extends Marker
{
    /**
     * @param Template\Context $context
     * @param Registry $registryHelper
     * @param Json $json
     * @param Arrays $arrays
     * @param Variables $variables
     * @param array $data
     * @phpstan-param array<string, mixed> $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registryHelper,
        protected Json $json,
        protected Arrays $arrays,
        protected Variables $variables,
        array $data = []
    ) {
        parent::__construct($context, $registryHelper, $data);
    }

    /**
     * Sets the template.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->setTemplate('Infrangible_GoogleMaps::json_marker.phtml');

        parent::_construct();
    }

    /**
     * Returns the JSON of the markers; the widget stores {} as <<< and >>>.
     *
     * @return string
     */
    public function getMarkerJson(): string
    {
        $markerJson = str_replace(
            ['<<<', '>>>'],
            ['{', '}'],
            htmlspecialchars_decode($this->getStringData('marker_json'))
        );

        // phpcs:ignore Magento2.Functions.DiscouragedFunction -- widgets may store the JSON with escaped quotes
        return $this->json->decode($markerJson) === null ? stripslashes($markerJson) : $markerJson;
    }

    /**
     * Returns the markers of the JSON.
     *
     * @return array
     * @phpstan-return array<int|string, mixed>
     */
    public function getLocations(): array
    {
        $markerJson = $this->getMarkerJson();
        // phpcs:ignore Magento2.Functions.DiscouragedFunction -- widgets may store the JSON with escaped quotes
        $locations = $this->json->decode($markerJson) ?? $this->json->decode(stripslashes($markerJson));

        return is_array($locations) ? $locations : [];
    }

    /**
     * Returns the markers as JSON that can be part of a script element.
     *
     * @return string
     */
    public function getLocationsJson(): string
    {
        $json = json_encode(
            array_values($this->getLocations()),
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
        );

        return $json === false ? '[]' : $json;
    }

    /**
     * Returns the center of the markers (latitude, longitude), or 0, 0 without markers.
     *
     * @return float[]
     */
    public function getMapCenter(): array
    {
        $x = $y = $z = 0.0;
        $count = 0;

        foreach ($this->getLocations() as $location) {
            $latitude = is_array($location) ? $location[ 'lat' ] ?? null : null;
            $longitude = is_array($location) ? $location[ 'lng' ] ?? null : null;

            if (! is_numeric($latitude) || ! is_numeric($longitude)) {
                continue;
            }

            $latitude = deg2rad((float)$latitude);
            $longitude = deg2rad((float)$longitude);

            $x += cos($latitude) * cos($longitude);
            $y += cos($latitude) * sin($longitude);
            $z += sin($latitude);
            $count++;
        }

        if ($count === 0) {
            return [0.0, 0.0];
        }

        $x /= $count;
        $y /= $count;
        $z /= $count;

        return [rad2deg(atan2($z, sqrt($x * $x + $y * $y))), rad2deg(atan2($y, $x))];
    }
}
