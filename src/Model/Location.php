<?php

declare(strict_types=1);

namespace Infrangible\GoogleMaps\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 *
 * @method int getLocationId()
 * @method void setLocationId(int $locationId)
 * @method string getKey()
 * @method void setKey(string $key)
 * @method string getLocation()
 * @method void setLocation(string $location)
 */
class Location
    extends AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Location::class);
    }
}
