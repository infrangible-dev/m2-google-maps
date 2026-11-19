<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\GoogleMaps\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * The table google_maps_location.
 */
class Location extends AbstractDb
{
    /**
     * Sets the table and the ID field.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('google_maps_location', 'location_id');
    }
}
