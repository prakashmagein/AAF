<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Indexer\Stock\Table\Column\Msi;

class GetStockColumnAlias implements GetColumnAliasInterface
{
    public function execute(int $stockId): string
    {
        return sprintf('stock_status_stock_%d', $stockId);
    }
}
