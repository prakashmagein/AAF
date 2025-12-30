<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\View;

use Amasty\ReportBuilder\Api\Data\ReportInterface;
use Amasty\ReportBuilder\Model\EntityScheme\Column\FilterConditionType;
use Amasty\ReportBuilder\Model\Report\ColumnProvider;
use Amasty\ReportBuilder\Model\ResourceModel\Report\Data\Collection;
use Amasty\ReportBuilder\Model\SelectResolver\ColumnBuilder\Eav\Relation\StoreIdRegistry;

class FilterApplier
{
    /**
     * @var FiltersProvider
     */
    private $filtersProvider;

    /**
     * @var StoreIdRegistry
     */
    private $storeIdRegistry;

    /**
     * @var ColumnProvider
     */
    private $columnProvider;

    public function __construct(
        FiltersProvider $filtersProvider,
        StoreIdRegistry $storeIdRegistry,
        ColumnProvider $columnProvider
    ) {
        $this->filtersProvider = $filtersProvider;
        $this->storeIdRegistry = $storeIdRegistry;
        $this->columnProvider = $columnProvider;
    }

    public function execute(ReportInterface $report, Collection $collection): void
    {
        $this->addDateFilter($report, $collection);
        $collection->setInterval($this->filtersProvider->getInterval());
        $this->saveStoreView();
    }

    private function addDateFilter(ReportInterface $report, Collection $collection): void
    {
        $columns = $this->columnProvider->getColumnsByReportId($report->getReportId());
        foreach ($columns as $column) {
            if ($column->getIsDateFilter()) {
                $collection->addFieldToFilter(
                    $column->getColumnAlias(),
                    $this->filtersProvider->getDateFilter()
                );
                break;
            }
        }
    }

    /**
     * Save store view in registry.
     * Used for show data by store (EAV).
     */
    private function saveStoreView(): void
    {
        $storeId = $this->filtersProvider->getStoreId();
        if ($storeId) {
            $this->storeIdRegistry->setStoreId($storeId);
        }
    }
}
