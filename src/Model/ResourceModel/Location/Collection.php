<?php

declare(strict_types=1);

namespace Infrangible\GoogleMaps\Model\ResourceModel\Feature;

use Infrangible\GoogleMaps\Model\Location;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Collection
    extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Location::class, \Infrangible\GoogleMaps\Model\ResourceModel\Location::class);
    }
}
