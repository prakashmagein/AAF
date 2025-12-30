<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\ColumnFilter;

interface FilterResolverInterface
{
    /**
     * Get all filters as array
     *
     * @return array
     */
    public function resolve(): ?array;
}
