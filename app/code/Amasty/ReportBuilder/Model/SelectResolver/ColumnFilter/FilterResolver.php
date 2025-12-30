<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\ColumnFilter;

use Amasty\Base\Model\Serializer;
use Amasty\ReportBuilder\Api\Data\ReportColumnInterface;
use Amasty\ReportBuilder\Model\EntityScheme\Provider;
use Amasty\ReportBuilder\Model\Report\ColumnsResolver;

class FilterResolver implements FilterResolverInterface
{
    /**
     * @var FilterStorageInterface
     */
    private $storage;

    /**
     * @var FilterConditionResolver
     */
    private $conditionResolver;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var ColumnsResolver
     */
    private $columnsResolver;

    /**
     * @var Provider
     */
    private $schemeProvider;

    public function __construct(
        FilterStorageInterface $storage,
        FilterConditionResolver $conditionResolver,
        Serializer $serializer,
        ColumnsResolver $columnsResolver,
        Provider $schemeProvider
    ) {
        $this->storage = $storage;
        $this->conditionResolver = $conditionResolver;
        $this->serializer = $serializer;
        $this->columnsResolver = $columnsResolver;
        $this->schemeProvider = $schemeProvider;
    }

    public function resolve(): ?array
    {
        $filters = $this->storage->getAllFilters();
        if (empty($filters)) {
            foreach ($this->columnsResolver->getReportColumns() as $reportColumn) {
                if (!$reportColumn->getVisibility() && $reportColumn->getFilter()) {
                    $this->addInvisibleColumnToFilter($reportColumn);
                }
            }
        }

        return $this->storage->getAllFilters();
    }

    /**
     * Process Invisible columns with filters.
     *
     * Invisible columns is static and cannot be changed on grid UI.
     * The method adds filters of invisible column manually.
     */
    private function addInvisibleColumnToFilter(ReportColumnInterface $reportColumn): void
    {
        $schemeColumn = $this->schemeProvider->getEntityScheme()->getColumnById($reportColumn->getColumnId());
        if ($schemeColumn === null) {
            return;
        }

        $parentColumn = $schemeColumn->getParentColumn() ?: $schemeColumn;
        $filterValue = $reportColumn->getFilter();
        if (!is_array($filterValue)) {
            $filterValue = $this->serializer->unserialize($filterValue);
        }
        $condition = $this->conditionResolver->resolve($parentColumn->getType(), $filterValue);

        $this->storage->addFilter($reportColumn->getColumnId(), $condition);
    }
}
