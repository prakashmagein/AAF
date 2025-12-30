<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\ResourceModel\Indexer\Stock\Select;

use Magento\Framework\DB\Select;

interface BuilderInterface
{
    /**
     * Return array of joined columns.
     *
     * @param Select $select
     * @return array
     */
    public function execute(Select $select): array;
}
