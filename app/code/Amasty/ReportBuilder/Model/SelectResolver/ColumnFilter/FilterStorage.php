<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\ColumnFilter;

class FilterStorage implements FilterStorageInterface
{
    /**
     * @var array
     */
    private $filters = [];

    public function addFilter(string $columnName, array $condition): void
    {
        if (isset($this->filters[$columnName])) {
            $this->filters[$columnName] += $condition;
        } else {
            $this->filters[$columnName] = $condition;
        }
    }

    public function removeFilter(string $columnName): void
    {
        unset($this->filters[$columnName]);
    }

    public function removeAllFilters(): void
    {
        $this->filters = [];
    }

    public function getAllFilters(): array
    {
        return $this->filters;
    }
}
