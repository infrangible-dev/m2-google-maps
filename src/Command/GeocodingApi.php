<?php

declare(strict_types=1);

namespace Infrangible\GoogleMaps\Command;

use Infrangible\Core\Console\Command\Command;
use Magento\Framework\App\Area;
use Symfony\Component\Console\Input\InputOption;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class GeocodingApi
    extends Command
{
    protected function getCommandName(): string
    {
        return 'google:maps:geocoding';
    }

    protected function getCommandDescription(): string
    {
        return 'Execute a Google Maps Geocoding API request';
    }

    protected function getCommandDefinition(): array
    {
        return [
            new InputOption('address', null, InputOption::VALUE_REQUIRED, 'The address to search'),
            new InputOption(
                'components',
                null,
                InputOption::VALUE_OPTIONAL,
                'The components to filter separated by comma, default: country:DE',
                'country:DE'
            ),
            new InputOption(
                'language', null, InputOption::VALUE_OPTIONAL, 'The language of the result, default: de', 'de'
            )
        ];
    }

    protected function getClassName(): string
    {
        return Script\GeocodingApi::class;
    }

    protected function getArea(): string
    {
        return Area::AREA_FRONTEND;
    }
}
