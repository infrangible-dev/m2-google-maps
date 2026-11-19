<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\GoogleMaps\Command\Script;

use FeWeDev\Base\Json;
use FeWeDev\Base\Variables;
use Infrangible\Core\Console\Command\Script;
use Infrangible\GoogleMaps\Model\GeocodingApiFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Searches the location of the address of the option --address and prints the result as JSON.
 */
class GeocodingApi extends Script
{
    /**
     * @param Variables $variables
     * @param Json $json
     * @param GeocodingApiFactory $geocodingApiFactory
     */
    public function __construct(
        protected Variables $variables,
        protected Json $json,
        protected GeocodingApiFactory $geocodingApiFactory
    ) {
    }

    /**
     * Searches the location and prints it; fails if nothing was found.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $address = $this->variables->stringValueOrDefault($input->getOption('address'));
        $language = strtolower(trim($this->variables->stringValueOrDefault($input->getOption('language'))));

        $searchResult = $this->geocodingApiFactory->create()->searchLocation(
            $address,
            $this->getComponents($this->variables->stringValueOrDefault($input->getOption('components'))),
            $language === '' ? 'de' : $language
        );

        if (count($searchResult) === 0) {
            $output->writeln(sprintf('<error>No location found for address: %s</error>', $address));

            return Command::FAILURE;
        }

        $output->writeln((string)$this->json->encode($searchResult, true, true));

        return Command::SUCCESS;
    }

    /**
     * Returns the filters of the option, for example country:DE,postal_code:07743; country:DE without option.
     *
     * @param string $componentsInput
     *
     * @return array<string, string>
     */
    public function getComponents(string $componentsInput): array
    {
        $components = [];

        foreach (explode(',', $componentsInput) as $component) {
            $parts = explode(':', $component, 2);

            if (count($parts) === 2 && trim($parts[ 0 ]) !== '' && trim($parts[ 1 ]) !== '') {
                $components[ trim($parts[ 0 ]) ] = trim($parts[ 1 ]);
            }
        }

        return count($components) === 0 ? ['country' => 'DE'] : $components;
    }
}
