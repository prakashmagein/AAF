<?php

/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @author     Hassan <support@fmeextensions.com>
 * @package   FME_Refund
 * @copyright Copyright (c) 2021 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */

namespace FME\Refund\Setup;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Zend_Db_Exception;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @throws Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $table = $setup->getConnection()->newTable($setup->getTable('fme_refund_order')
        )->addColumn(

            'refund_id',
            Table::TYPE_INTEGER,null,['identity' => true,'nullable' => false,'primary'=>true],

        )->addColumn(

            'customer_name',
            Table::TYPE_TEXT,1234,['nullable'=>false],

        )->addColumn(

            'customer_email',
            Table::TYPE_TEXT,1234,['nullable'=>false],

        )->addColumn(

            'refund_reason',
            Table::TYPE_TEXT,1234,['nullable'=>false],

        )->addColumn(

            'description',
            Table::TYPE_TEXT,1234,['nullable'=>false],

        )->addColumn(

            'status',
            Table::TYPE_TEXT,50,['nullable'=>true,'default'=>'pending'],

        )->addColumn(

            'date',
            Table::TYPE_DATETIME,255,['nullable'=>true],

        )->addColumn(

            'order_id',
            Table::TYPE_INTEGER,255,['nullable'=>false]
        );

        $setup->getConnection()->createTable($table);
        $setup->endSetup();

    }
}

