<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\GoogleMaps\Block\Widget\GoogleMaps;

/**
 * Widget with an input that suggests addresses of Google Maps (Places Autocomplete).
 */
class Autocomplete extends Maps
{
    /**
     * Sets the template.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->setTemplate('Infrangible_GoogleMaps::autocomplete.phtml');

        parent::_construct();
    }

    /**
     * Returns the ID and name of the input.
     *
     * @return string
     */
    public function getInputId(): string
    {
        return $this->getStringData('input_id');
    }

    /**
     * Returns the restrictions of the suggestions as JavaScript object properties, for example country: ["de", "at"].
     *
     * @return string
     */
    public function getComponentRestrictions(): string
    {
        return htmlspecialchars_decode($this->getStringData('component_restrictions'));
    }

    /**
     * Returns the value of the input of the request, or null.
     *
     * @return string|null
     */
    public function getInputValue(): ?string
    {
        $value = $this->getRequest()->getParam($this->getInputId());

        return is_scalar($value) ? (string)$value : null;
    }
}
