<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\SelectColumn;

interface ColumnResolverInterface
{
    /**
     * Get all prepared columns of a report for building sql query
     *
     * @return ColumnStorageInterface
     */
    public function resolve(): ColumnStorageInterface;
}
