<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\ColumnFilter;

interface FilterModifierInterface
{
    /**
     * Modify filter in storage
     *
     * @param string $columnName
     * @param array $condition
     */
    public function modify(string $columnName, ?array $condition = null): void;
}
