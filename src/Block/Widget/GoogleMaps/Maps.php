<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\GoogleMaps\Block\Widget\GoogleMaps;

use Infrangible\Core\Helper\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

/**
 * Base of the widgets that use the Google Maps API: a rendered widget lets the page load the API (see Api).
 */
abstract class Maps extends Template implements BlockInterface
{
    /**
     * @param Template\Context $context
     * @param Registry $registryHelper
     * @param array $data
     * @phpstan-param array<string, mixed> $data
     */
    public function __construct(Template\Context $context, protected Registry $registryHelper, array $data = [])
    {
        parent::__construct($context, $data);
    }

    /**
     * Renders the widget and registers that the page needs the API.
     *
     * @return string
     */
    protected function _toHtml(): string
    {
        $this->registryHelper->register('google_maps_api', true, true);

        return parent::_toHtml();
    }

    /**
     * Returns a string value of the widget, or an empty string.
     *
     * @param string $key
     *
     * @return string
     */
    protected function getStringData(string $key): string
    {
        $value = $this->getData($key);

        return is_scalar($value) ? trim((string)$value) : '';
    }
}
