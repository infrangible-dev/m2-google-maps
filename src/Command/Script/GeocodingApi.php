<?php

declare(strict_types=1);

namespace Infrangible\GoogleMaps\Command\Script;

use FeWeDev\Base\Variables;
use Infrangible\Core\Console\Command\Script;
use Infrangible\GoogleMaps\Model\GeocodingApiFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class GeocodingApi
    extends Script
{
    /** @var Variables */
    protected $variables;

    /** @var GeocodingApiFactory */
    protected $geocodingApiFactory;

    public function __construct(Variables $variables, GeocodingApiFactory $geocodingApiFactory)
    {
        $this->variables = $variables;
        $this->geocodingApiFactory = $geocodingApiFactory;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $addressInput = $input->getOption('address');
        $componentsInput = $input->getOption('components');
        $languageInput = $input->getOption('language');

        if ($componentsInput === null || $this->variables->isEmpty($componentsInput)) {
            $components = ['country' => 'DE'];
        } else {
            $components = [];

            foreach (explode(',', $componentsInput) as $component) {
                foreach (explode(':', $component) as $filter => $value) {
                    $components[$filter] = $value;
                }
            }
        }

        if ($languageInput === null || $this->variables->isEmpty($languageInput)) {
            $language = 'de';
        } else {
            $language = strtolower($languageInput);
        }

        $geocodingApi = $this->geocodingApiFactory->create();

        $searchResult = $geocodingApi->searchLocation($addressInput, $components, $language);

        print_r($searchResult);

        return 0;
    }
}