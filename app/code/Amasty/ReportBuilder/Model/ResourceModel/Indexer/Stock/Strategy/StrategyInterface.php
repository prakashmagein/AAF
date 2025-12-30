<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\ResourceModel\Indexer\Stock\Strategy;

use Amasty\ReportBuilder\Model\ResourceModel\Indexer\Stock\Select\BuilderInterface;
use Magento\Framework\DB\Select;

interface StrategyInterface
{
    /**
     * @param Select $select
     * @return void
     */
    public function filter(Select $select): void;

    /**
     * @return BuilderInterface[]
     */
    public function getSelectBuilders(): array;
}
