<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;

class LoadStocks
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Returned all stocks.
     * @return array Stock Id => Stock Name
     */
    public function execute(): array
    {
        $inventoryStockTableName = $this->resourceConnection->getTableName('inventory_stock');

        if ($this->resourceConnection->getConnection()->isTableExists($inventoryStockTableName)) {
            $select = $this->resourceConnection->getConnection()->select()->from(
                $inventoryStockTableName,
                ['stock_id', 'name']
            );

            return $this->resourceConnection->getConnection()->fetchPairs($select);
        }

        return [];
    }
}
