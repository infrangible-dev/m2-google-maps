<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\GoogleMaps\Block\Widget\GoogleMaps;

/**
 * Widget with a map and one marker, with a title and an info window.
 */
class SimpleMarker extends Marker
{
    /**
     * Sets the template.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->setTemplate('Infrangible_GoogleMaps::simple_marker.phtml');

        parent::_construct();
    }

    /**
     * Returns the latitude of the marker.
     *
     * @return float
     */
    public function getLatitude(): float
    {
        return (float)$this->getStringData('lat');
    }

    /**
     * Returns the longitude of the marker.
     *
     * @return float
     */
    public function getLongitude(): float
    {
        return (float)$this->getStringData('lng');
    }

    /**
     * Returns the title of the marker as plain text.
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        // the widget stores its parameters with HTML entities, the title is plain text
        $title = htmlspecialchars_decode($this->getStringData('title'), ENT_QUOTES | ENT_HTML5);

        return $title === '' ? null : $title;
    }

    /**
     * Returns the HTML of the info window, without line breaks.
     *
     * @return string
     */
    public function getInfoWindow(): string
    {
        return (string)preg_replace("/[\r\n]/", '', htmlspecialchars_decode($this->getStringData('info_window')));
    }
}
