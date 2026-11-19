<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\GoogleMaps\Block\Widget\GoogleMaps;

use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Registry;
use Infrangible\Core\Helper\Stores;
use Magento\Framework\View\Element\Template;

/**
 * Loads the Google Maps API at the end of pages with a widget of the module, if enabled and an API key is configured.
 */
class Api extends Template
{
    /** URL of the script of the API. */
    public const API_URL = 'https://maps.googleapis.com/maps/api/js';

    /**
     * @param Template\Context $context
     * @param Registry $registryHelper
     * @param Stores $storeHelper
     * @param Variables $variables
     * @param array $data
     * @phpstan-param array<string, mixed> $data
     */
    public function __construct(
        Template\Context $context,
        protected Registry $registryHelper,
        protected Stores $storeHelper,
        protected Variables $variables,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Tells whether Google Maps is enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->storeHelper->getStoreConfigFlag('infrangible_googlemaps/settings/enabled', true);
    }

    /**
     * Returns the API key, or null.
     *
     * @return string|null
     */
    public function getApiKey(): ?string
    {
        $apiKey = trim(
            $this->variables->stringValueOrDefault(
                $this->storeHelper->getStoreConfig('infrangible_googlemaps/settings/api_key')
            )
        );

        return $apiKey === '' ? null : $apiKey;
    }

    /**
     * Returns the language of the store, for example de of de_DE.
     *
     * @return string
     */
    public function getLanguage(): string
    {
        $locale = $this->variables->stringValueOrDefault($this->storeHelper->getStoreConfig('general/locale/code'));

        return explode('_', $locale)[ 0 ];
    }

    /**
     * Returns the URL of the script of the API with key, language and the library places.
     *
     * @return string
     */
    public function getScriptUrl(): string
    {
        return self::API_URL . '?' . http_build_query(
            [
                'key'       => $this->getApiKey(),
                'language'  => $this->getLanguage(),
                'libraries' => 'places',
                'callback'  => 'googleMapsApi',
                'v'         => 'weekly'
            ]
        );
    }

    /**
     * Renders the script if a widget of the page needs it.
     *
     * @return string
     */
    protected function _toHtml(): string
    {
        if ($this->isEnabled() && $this->registryHelper->registry('google_maps_api') && $this->getApiKey() !== null) {
            return parent::_toHtml();
        }

        return '';
    }
}
