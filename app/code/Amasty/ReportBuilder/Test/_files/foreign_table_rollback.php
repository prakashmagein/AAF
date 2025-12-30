<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\TestFramework\Helper\Bootstrap;

/** @var AdapterInterface $resource */
$connection = Bootstrap::getObjectManager()->get(ResourceConnection::class)->getConnection();

$connection->dropTable('amasty_test_order_item_foreign');
