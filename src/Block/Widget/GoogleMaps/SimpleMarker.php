<?php

declare(strict_types=1);

namespace Infrangible\GoogleMaps\Block\Widget\GoogleMaps;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class SimpleMarker
    extends Marker
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->setTemplate('Infrangible_GoogleMaps::simple_marker.phtml');

        parent::_construct();
    }

    /**
     * @return string
     */
    public function getLatitude(): string
    {
        return $this->getData('lat');
    }

    /**
     * @return string
     */
    public function getLongitude(): string
    {
        return $this->getData('lng');
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->getData('title');
    }

    /**
     * @return string
     */
    public function getInfoWindow(): string
    {
        $infoWindow = $this->getData('info_window');

        return $infoWindow === null ? '' : htmlspecialchars_decode($infoWindow);
    }
}
