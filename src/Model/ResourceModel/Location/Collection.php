<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\GoogleMaps\Model\ResourceModel\Location;

use Infrangible\GoogleMaps\Model\Location;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * The cached results of the Geocoding API.
 */
class Collection extends AbstractCollection
{
    /**
     * Sets the model and the resource model.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(Location::class, \Infrangible\GoogleMaps\Model\ResourceModel\Location::class);
    }
}
