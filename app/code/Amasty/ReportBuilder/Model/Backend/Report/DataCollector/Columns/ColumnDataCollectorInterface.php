<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Backend\Report\DataCollector\Columns;

use Amasty\ReportBuilder\Api\Data\ReportColumnInterface;

interface ColumnDataCollectorInterface
{
    /**
     * Set additional data to data model.
     *
     * @param ReportColumnInterface $column
     * @param array $columnData
     */
    public function collectData(ReportColumnInterface $column, array $columnData): void;
}
