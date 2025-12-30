<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\ColumnOrder;

use Amasty\ReportBuilder\Model\ResourceModel\Report\Data\Select;

interface OrderModifierInterface
{
    /**
     * Modify order in storage
     *
     * @param string $columnName
     * @param string $direction
     */
    public function modify(string $columnName, string $direction = Select::SQL_ASC): void;
}
