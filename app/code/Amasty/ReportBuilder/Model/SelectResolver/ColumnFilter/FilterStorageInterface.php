<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\ColumnFilter;

interface FilterStorageInterface
{
    /**
     * Add filter to storage
     *
     * @param string $columnName
     * @param array $condition
     */
    public function addFilter(string $columnName, array $condition): void;

    /**
     * Remove filter from storage
     *
     * @param string $columnName
     */
    public function removeFilter(string $columnName): void;

    /**
     * Remove all existed filters
     *
     * @return void
     */
    public function removeAllFilters(): void;

    /**
     * Get all existed filters
     *
     * @return array
     */
    public function getAllFilters(): array;
}
