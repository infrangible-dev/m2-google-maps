<?php

declare(strict_types=1);

namespace Infrangible\GoogleMaps\Block\Widget\GoogleMaps;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
abstract class Marker
    extends Maps
{
    /**
     * @return string
     */
    public function getMapId(): string
    {
        return $this->getData('map_id');
    }

    /**
     * @return string|null
     */
    public function getWidth(): ?string
    {
        $value = $this->getData('width');

        return $value === null ? null : strval($value);
    }

    /**
     * @return string|null
     */
    public function getHeight(): ?string
    {
        $value = $this->getData('height');

        return $value === null ? null : strval($value);
    }

    /**
     * @return int|null
     */
    public function getZoom(): ?int
    {
        $value = $this->getData('zoom');

        return $value === null ? null : intval($value);
    }

    /**
     * @return string
     */
    public function getZoomControl(): string
    {
        return $this->getData('zoom_control') ? 'true' : 'false';
    }

    /**
     * @return string
     */
    public function getMapTypeControl(): string
    {
        return $this->getData('map_type_control') ? 'true' : 'false';
    }

    /**
     * @return string
     */
    public function getStreetViewControl(): string
    {
        return $this->getData('street_view_control') ? 'true' : 'false';
    }

    /**
     * @return string
     */
    public function getRotateControl(): string
    {
        return $this->getData('rotate_control') ? 'true' : 'false';
    }

    /**
     * @return string
     */
    public function getScaleControl(): string
    {
        return $this->getData('scale_control') ? 'true' : 'false';
    }

    /**
     * @return string
     */
    public function getFullscreenControl(): string
    {
        return $this->getData('fullscreen_control') ? 'true' : 'false';
    }

    /**
     * @return string
     */
    public function getAdministrativeStyle(): string
    {
        return $this->getData('administrative') ? 'on' : 'off';
    }

    /**
     * @return string
     */
    public function getLandscapeStyle(): string
    {
        return $this->getData('landscape') ? 'on' : 'off';
    }

    /**
     * @return string
     */
    public function getPOIStyle(): string
    {
        return $this->getData('poi') ? 'on' : 'off';
    }

    /**
     * @return string
     */
    public function getRoadStyle(): string
    {
        return $this->getData('road') ? 'on' : 'off';
    }

    /**
     * @return string
     */
    public function getTransitStyle(): string
    {
        return $this->getData('transit') ? 'on' : 'off';
    }

    /**
     * @return string
     */
    public function getWaterStyle(): string
    {
        return $this->getData('water') ? 'on' : 'off';
    }
}
