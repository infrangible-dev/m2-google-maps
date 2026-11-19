<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\GoogleMaps\Block\Widget\GoogleMaps;

/**
 * Base of the widgets with a map: size, zoom, controls and visible features.
 */
abstract class Marker extends Maps
{
    /**
     * Returns the ID of the element of the map.
     *
     * @return string
     */
    public function getMapId(): string
    {
        return $this->getStringData('map_id');
    }

    /**
     * Returns the width of the map: pixels or a CSS length, or null.
     *
     * @return string|null
     */
    public function getWidth(): ?string
    {
        $width = $this->getStringData('width');

        return $width === '' ? null : $width;
    }

    /**
     * Returns the height of the map: pixels or a CSS length, or null.
     *
     * @return string|null
     */
    public function getHeight(): ?string
    {
        $height = $this->getStringData('height');

        return $height === '' ? null : $height;
    }

    /**
     * Returns the zoom of the map, or null for the zoom of Google Maps.
     *
     * @return int|null
     */
    public function getZoom(): ?int
    {
        $zoom = $this->getData('zoom');

        return is_numeric($zoom) ? (int)$zoom : null;
    }

    /**
     * Returns whether the map shows the zoom control, as JavaScript value.
     *
     * @return string
     */
    public function getZoomControl(): string
    {
        return $this->getFlag('zoom_control');
    }

    /**
     * Returns whether the map shows the map type control, as JavaScript value.
     *
     * @return string
     */
    public function getMapTypeControl(): string
    {
        return $this->getFlag('map_type_control');
    }

    /**
     * Returns whether the map shows the street view control, as JavaScript value.
     *
     * @return string
     */
    public function getStreetViewControl(): string
    {
        return $this->getFlag('street_view_control');
    }

    /**
     * Returns whether the map shows the rotate control, as JavaScript value.
     *
     * @return string
     */
    public function getRotateControl(): string
    {
        return $this->getFlag('rotate_control');
    }

    /**
     * Returns whether the map shows the scale control, as JavaScript value.
     *
     * @return string
     */
    public function getScaleControl(): string
    {
        return $this->getFlag('scale_control');
    }

    /**
     * Returns whether the map shows the fullscreen control, as JavaScript value.
     *
     * @return string
     */
    public function getFullscreenControl(): string
    {
        return $this->getFlag('fullscreen_control');
    }

    /**
     * Returns the visibility of administrative areas.
     *
     * @return string
     */
    public function getAdministrativeStyle(): string
    {
        return $this->getVisibility('administrative');
    }

    /**
     * Returns the visibility of landscapes.
     *
     * @return string
     */
    public function getLandscapeStyle(): string
    {
        return $this->getVisibility('landscape');
    }

    /**
     * Returns the visibility of points of interest.
     *
     * @return string
     */
    public function getPOIStyle(): string
    {
        return $this->getVisibility('poi');
    }

    /**
     * Returns the visibility of roads.
     *
     * @return string
     */
    public function getRoadStyle(): string
    {
        return $this->getVisibility('road');
    }

    /**
     * Returns the visibility of transit stations and lines.
     *
     * @return string
     */
    public function getTransitStyle(): string
    {
        return $this->getVisibility('transit');
    }

    /**
     * Returns the visibility of bodies of water.
     *
     * @return string
     */
    public function getWaterStyle(): string
    {
        return $this->getVisibility('water');
    }

    /**
     * Returns the CSS of the width, or null.
     *
     * @return string|null
     */
    public function getWidthStyle(): ?string
    {
        return $this->getSizeStyle('width', $this->getWidth());
    }

    /**
     * Returns the CSS of the height, or null.
     *
     * @return string|null
     */
    public function getHeightStyle(): ?string
    {
        return $this->getSizeStyle('height', $this->getHeight());
    }

    /**
     * Returns a flag of the widget as JavaScript value.
     *
     * @param string $key
     *
     * @return string
     */
    private function getFlag(string $key): string
    {
        return $this->getData($key) ? 'true' : 'false';
    }

    /**
     * Returns the visibility of a feature of the map.
     *
     * @param string $key
     *
     * @return string
     */
    private function getVisibility(string $key): string
    {
        return $this->getData($key) ? 'on' : 'off';
    }

    /**
     * Returns the CSS of a size.
     *
     * A number is a length in pixels, other values have to be CSS lengths (for example 100% or 20rem); anything else
     * is ignored, so a value cannot add other CSS.
     *
     * @param string $property
     * @param string|null $value
     *
     * @return string|null
     */
    private function getSizeStyle(string $property, ?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_numeric($value)) {
            return sprintf('%s: %spx;', $property, $value);
        }

        return preg_match('/^\d+(\.\d+)?(px|%|em|rem|vh|vw)$/', $value) === 1 ?
            sprintf('%s: %s;', $property, $value) : null;
    }
}
