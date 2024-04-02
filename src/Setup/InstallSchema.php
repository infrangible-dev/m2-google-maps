<?php

declare(strict_types=1);

namespace Infrangible\GoogleMaps\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class InstallSchema
    implements InstallSchemaInterface
{
    /**
     * @throws \Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $connection = $setup->getConnection();

        if (!$connection->isTableExists('google_maps_location')) {
            $googleMapsLocationTable = $connection->newTable('google_maps_location');

            $googleMapsLocationTable->addColumn(
                'location_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true
                ],
                'Search Id'
            );
            $googleMapsLocationTable->addColumn('key', Table::TYPE_TEXT, 32, ['nullable' => false], 'Key');
            $googleMapsLocationTable->addColumn('location', Table::TYPE_TEXT, 256, ['nullable' => false], 'Location');

            $googleMapsLocationTable->addIndex(
                $connection->getIndexName('google_maps_location', ['key']),
                ['key'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            );

            $connection->createTable($googleMapsLocationTable);
        }

        $setup->endSetup();
    }
}
