<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_TableRateShipping
 * @copyright  Copyright (c) 2022 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\ProductShipping\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $connection = $installer->getConnection();

        //Update for version 1.0.1
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $connection->dropTable($connection->getTableName('lof_ps_rate'));
            $connection->dropTable($connection->getTableName('lof_ps_rate_method'));
            $connection->dropTable($connection->getTableName('lof_ps_rate_product'));

            /**
             * Table: lof_ps_rate
             */
            $table = $installer->getConnection()
                ->newTable($installer->getTable('lof_ps_rate'))
                ->addColumn(
                    'lofshipping_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Lof Product Shipping ID'
                )
                ->addColumn(
                    'website_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Website ID'
                )
                ->addColumn(
                    'dest_country_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => true],
                    'Destination coutry ISO/2 or ISO/3 code'
                )
                ->addColumn(
                    'dest_region_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => true],
                    'Destination Region Id'
                )
                ->addColumn(
                    'dest_zip',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => true],
                    'Destination Post Code (Zip), starting from this value, or * as any value are accepted'
                )
                ->addColumn(
                    'dest_zip_to',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => true],
                    'Destination Post Code (Zip), smaller than this value, or * as any value are accepted'
                )
                ->addColumn(
                    'quantity_from',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => true],
                    'From a specific quantity amount or value of * as accept any value'
                )
                ->addColumn(
                    'quantity_to',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => true],
                    'Bellow a specific quantity amount or value of * as accept any value'
                )
                ->addColumn(
                    'price',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '12,4',
                    ['unsigned' => true, 'nullable' => false, 'default' => '0.0000'],
                    'Price'
                )
                ->addColumn(
                    'weight_from',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'Weight from, acceptable starting value, or * mean any value are accepted'
                )
                ->addColumn(
                    'weight_to',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => true],
                    'Weight to, acceptable ending value, or * mean any value are accepted'
                )
                ->addColumn(
                    'priority',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '100']
                )
                ->addColumn(
                    'partner_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Partner Id'
                )
                ->addColumn(
                    'shipping_method_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Shipping Method Id'
                )->setComment('Marketplace Table rate shipping table');
            $installer->getConnection()->createTable($table);

            /**
             * Table: lof_ps_rate_method
             */
            $table = $installer->getConnection()
                ->newTable($installer->getTable('lof_ps_rate_method'))
                ->addColumn(
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Index ID'
                )
                ->addColumn(
                    'method_name',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => true],
                    'Shipping Method name'
                )
                ->addColumn(
                    'partner_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' =>  '0'],
                    'Partner ID'
                )
                ->setComment('Lof Product rate shipping methods table');
            $installer->getConnection()->createTable($table);

            /**
             * Table: lof_ps_rate_product
             */
            $table = $installer->getConnection()
                ->newTable($installer->getTable('lof_ps_rate_product'))
                ->addColumn(
                    'lofshipping_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Lof ps shipping ID'
                )
                ->addColumn(
                    'position',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    11,
                    ['nullable' => true],
                    'Position'
                )
                ->addColumn(
                    'product_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' =>  '0'],
                    'Product ID'
                )
                ->setComment('Lof Product rate shipping methods table');
            $installer->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            /** Add new colums for table price */
            $table = $installer->getTable('lof_ps_rate');
            $installer->getConnection()->addColumn(
                $table,
                'allow_second_price',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length'   => 4,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '0',
                    'comment'  => 'Enable Second Price or not. Default: 0'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'second_price',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length'   => '12,4',
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '0.0000',
                    'comment'  => 'Shipping price apply from second product in cart.'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'cost',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'size'   => '12,4',
                    'nullable' => false,
                    'default'  => '0.0000',
                    'comment'  => 'Shipping Cost'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'allow_free_shipping',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length'   => 4,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '0',
                    'comment'  => 'Enable Free Shipping rule or not. Default: 0'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'free_shipping',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'size'   => '12,4',
                    'nullable' => false,
                    'default'  => '0.0000',
                    'comment'  => 'Apply free shipping when cart total equal or greater than free_shipping value. Set 0.0000 or 0 to disable'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            /** Add new colums for table price */
            $table = $installer->getTable('lof_ps_rate');
            $installer->getConnection()->addColumn(
                $table,
                'conditions_serialized',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => '2M',
                    'nullable' => true,
                    'comment'  => 'Conditions Serialized'
                ]
            );
        }
        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            /** Add new colums for table price */
            $table = $installer->getTable('sales_shipment');
            $installer->getConnection()->addColumn(
                $table,
                'product_shipping_method',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'size'   => '150',
                    'nullable' => true,
                    'comment'  => 'Product Shipping Method Name'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'product_shipping_rate',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'size'   => '12,4',
                    'nullable' => false,
                    'default'  => '0.0000',
                    'comment'  => 'Product Shipping Rate Price'
                ]
            );
        }
        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            /** Add new colums for table price */
            $table = $installer->getTable('lof_ps_rate');
            $installer->getConnection()->addColumn(
                $table,
                'price_for_unit',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length'   => '4',
                    'nullable' => true,
                    'default'  => 1,
                    'comment'  => 'Apply price for each item or not. If Yes, will apply price x item qty, if no, will apply as total price.'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'description',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'size'   => '150',
                    'nullable' => true,
                    'comment'  => 'Rating Description'
                ]
            );
        }
        $installer->endSetup();
    }
}
