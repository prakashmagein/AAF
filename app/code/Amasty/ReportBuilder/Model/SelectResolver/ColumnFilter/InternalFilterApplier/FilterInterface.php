<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\ColumnFilter\InternalFilterApplier;

use Amasty\ReportBuilder\Api\ColumnInterface;

interface FilterInterface
{
    /**
     * @param ColumnInterface $column
     * @param array $conditions
     * @return bool
     */
    public function apply(ColumnInterface $column, array $conditions): bool;
}
