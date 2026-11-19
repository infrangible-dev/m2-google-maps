<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\GoogleMaps\Test\Unit\Command\Script;

use FeWeDev\Base\Json;
use FeWeDev\Base\Variables;
use Infrangible\GoogleMaps\Command\Script\GeocodingApi;
use Infrangible\GoogleMaps\Model\GeocodingApiFactory;
use PHPUnit\Framework\TestCase;

/**
 * Tests the filters of the option --components.
 */
class GeocodingApiTest extends TestCase
{
    /**
     * Tests the filters; before, "country:DE" became the filters 0:country and 1:DE.
     *
     * @return void
     */
    public function testComponents(): void
    {
        $script = new GeocodingApi(new Variables(), new Json(), $this->createStub(GeocodingApiFactory::class));

        $this->assertSame(
            ['country' => 'DE', 'postal_code' => '07743'],
            $script->getComponents('country:DE, postal_code:07743')
        );
        $this->assertSame(['country' => 'DE'], $script->getComponents(''));
        $this->assertSame(['country' => 'AT'], $script->getComponents('country:AT,invalid'));
    }
}
