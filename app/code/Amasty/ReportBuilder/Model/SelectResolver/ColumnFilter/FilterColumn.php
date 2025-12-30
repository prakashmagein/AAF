<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\ColumnFilter;

use Amasty\ReportBuilder\Api\ColumnInterface;
use Amasty\ReportBuilder\Api\Data\SelectColumnInterface;
use Amasty\ReportBuilder\Model\EntityScheme\Column\AggregationType;
use Amasty\ReportBuilder\Model\EntityScheme\Provider;
use Amasty\ReportBuilder\Model\SelectResolver\EntitySimpleRelationResolver;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\ColumnResolverInterface;

class FilterColumn
{
    /**
     * @var ColumnResolverInterface
     */
    private $columnResolver;

    /**
     * @var Provider
     */
    private $schemeProvider;

    /**
     * @var EntitySimpleRelationResolver
     */
    private $simpleRelationResolver;

    public function __construct(
        ColumnResolverInterface $columnResolver,
        Provider $schemeProvider,
        EntitySimpleRelationResolver $simpleRelationResolver
    ) {
        $this->columnResolver = $columnResolver;
        $this->schemeProvider = $schemeProvider;
        $this->simpleRelationResolver = $simpleRelationResolver;
    }

    /**
     * If column without aggregation use where.
     * If column in sub-select and with aggregation use where. (Else used InternalFilterApplier).
     */
    public function isFilterCanUseWhere(string $filter): bool
    {
        $schemeColumn = $this->getEntityByFilterAlias($filter);

        if ($schemeColumn === null) {
            return false;
        }

        $selectColumn = $this->getSelectColumnByFilter($filter);

        return !$selectColumn
            || $schemeColumn->getAggregationType() === AggregationType::TYPE_NONE
            || !$this->simpleRelationResolver->isEntitySimple($selectColumn->getEntityName());
    }

    /**
     * @param string $filter
     *
     * @return null|ColumnInterface
     */
    public function getEntityByFilterAlias(string $filter): ?ColumnInterface
    {
        $selectColumn = $this->getSelectColumnByFilter($filter);

        if ($selectColumn !== null && $selectColumn->getAlias() === $filter) {
            return $this->schemeProvider->getEntityScheme()->getColumnById($selectColumn->getColumnId());
        }

        return null;
    }

    /**
     * @param string $filter
     *
     * @return SelectColumnInterface|null null - no column is select for provided filter
     */
    public function getSelectColumnByFilter(string $filter): ?SelectColumnInterface
    {
        $columnsRegistry = $this->columnResolver->resolve();
        if (strpos($filter, '.') !== false) {
            $filter = str_replace('.', '_', $filter);
        }

        return $columnsRegistry->getColumnByAlias($filter);
    }

    /**
     * Is column aggregated by join, not by grouping.
     */
    private function isColumnUseAggregation(ColumnInterface $schemeColumn): bool
    {
        $selectColumn = $this->columnResolver->resolve()->getColumnById($schemeColumn->getColumnId());

        return $selectColumn->isUseAggregation()
            && !$this->simpleRelationResolver->isEntitySimple($selectColumn->getEntityName());
    }
}
