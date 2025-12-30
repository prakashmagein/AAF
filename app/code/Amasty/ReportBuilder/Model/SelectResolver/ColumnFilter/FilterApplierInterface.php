<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\ColumnFilter;

use Amasty\ReportBuilder\Model\ResourceModel\Report\Data\Select;

interface FilterApplierInterface
{
    /**
     * Apply all filters to select
     *
     * @param Select $select
     */
    public function apply(Select $select): void;
}
