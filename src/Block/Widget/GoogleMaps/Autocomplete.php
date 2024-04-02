<?php

declare(strict_types=1);

namespace Infrangible\GoogleMaps\Block\Widget\GoogleMaps;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Autocomplete
    extends Maps
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->setTemplate('Infrangible_GoogleMaps::autocomplete.phtml');

        parent::_construct();
    }

    /**
     * @return string
     */
    public function getInputId(): string
    {
        return $this->getData('input_id');
    }

    /**
     * @return string
     */
    public function getComponentRestrictions(): string
    {
        $componentRestrictions = $this->getData('component_restrictions');

        return $componentRestrictions === null ? '' : htmlspecialchars_decode($componentRestrictions);
    }

    /**
     * @return string
     */
    public function getInputValue(): ?string
    {
        $request = $this->getRequest();

        return $request->getParam($this->getData('input_id'));
    }
}
