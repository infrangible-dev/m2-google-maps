<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\GoogleMaps\Command;

use Infrangible\Core\Console\Command\Command;
use Magento\Framework\App\Area;
use Symfony\Component\Console\Input\InputOption;

/**
 * The command google:maps:geocoding, which searches the location of an address.
 */
class GeocodingApi extends Command
{
    /**
     * Returns the name of the command.
     *
     * @return string
     */
    protected function getCommandName(): string
    {
        return 'google:maps:geocoding';
    }

    /**
     * Returns the description of the command.
     *
     * @return string
     */
    protected function getCommandDescription(): string
    {
        return 'Execute a Google Maps Geocoding API request';
    }

    /**
     * Returns the options of the command.
     *
     * @return InputOption[]
     */
    protected function getCommandDefinition(): array
    {
        return [
            new InputOption('address', null, InputOption::VALUE_REQUIRED, 'The address to search'),
            new InputOption(
                'components',
                null,
                InputOption::VALUE_OPTIONAL,
                'The components to filter separated by comma, for example: country:DE,postal_code:07743',
                'country:DE'
            ),
            new InputOption('language', null, InputOption::VALUE_OPTIONAL, 'The language of the result', 'de')
        ];
    }

    /**
     * Returns the script of the command.
     *
     * @return string
     */
    protected function getClassName(): string
    {
        return Script\GeocodingApi::class;
    }

    /**
     * Returns the area of the command.
     *
     * @return string
     */
    protected function getArea(): string
    {
        return Area::AREA_FRONTEND;
    }
}
