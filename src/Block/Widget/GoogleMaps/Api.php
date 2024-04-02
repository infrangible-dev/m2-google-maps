<?php

declare(strict_types=1);

namespace Infrangible\GoogleMaps\Block\Widget\GoogleMaps;

use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Registry;
use Infrangible\Core\Helper\Stores;
use Magento\Framework\View\Element\Template;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Api
    extends Template
{
    /** @var Registry */
    protected $registryHelper;

    /** @var Stores */
    protected $storeHelper;

    /** @var Variables */
    protected $variables;

    /**
     * @param Template\Context $context
     * @param Registry         $registryHelper
     * @param Stores           $storeHelper
     * @param Variables        $variables
     * @param array            $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registryHelper,
        Stores $storeHelper,
        Variables $variables,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->registryHelper = $registryHelper;
        $this->storeHelper = $storeHelper;
        $this->variables = $variables;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->storeHelper->getStoreConfig('infrangible_googlemaps/settings/enabled', true, true);
    }

    /**
     * @return string|null
     */
    public function getApiKey(): ?string
    {
        return $this->storeHelper->getStoreConfig('infrangible_googlemaps/settings/api_key');
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        $locale = $this->storeHelper->getStoreConfig('general/locale/code');

        $localeParts = explode('_', $locale);

        return reset($localeParts);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml(): string
    {
        if ($this->isEnabled() && $this->registryHelper->registry('google_maps_api')
            && !$this->variables->isEmpty($this->getApiKey())) {
            return parent::_toHtml();
        }

        return '';
    }
}
