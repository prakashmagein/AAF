<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\TestFramework\Helper\Bootstrap;

/** @var AdapterInterface $resource */
$connection = Bootstrap::getObjectManager()->get(ResourceConnection::class)->getConnection();

$table = $connection->newTable('amasty_test_order_item_foreign');
$table->addColumn(
    'order_item_id',
    Table::TYPE_INTEGER,
    10,
    ['nullable' => false, 'unsigned' => true, 'identity' => false]
)->addColumn(
    'is_foreign',
    Table::TYPE_BOOLEAN,
    null,
    ['default' => false, 'nullable' => false, 'unsigned' => true, 'identity' => false]
);

$connection->createTable($table);
