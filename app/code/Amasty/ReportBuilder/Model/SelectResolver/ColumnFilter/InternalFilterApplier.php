<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\ColumnFilter;

use Amasty\ReportBuilder\Api\ColumnInterface;
use Amasty\ReportBuilder\Model\EntityScheme\Column\AggregationType;
use Amasty\ReportBuilder\Model\SelectResolver\ColumnFilter\InternalFilterApplier\FilterInterface;
use Amasty\ReportBuilder\Model\SelectResolver\Context;
use Amasty\ReportBuilder\Model\SelectResolver\EntitySimpleRelationResolver;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\ColumnResolverInterface;

class InternalFilterApplier
{
    /**
     * @var ColumnResolverInterface
     */
    private $columnResolver;

    /**
     * @var EntitySimpleRelationResolver
     */
    private $simpleRelationResolver;

    /**
     * @var \Amasty\ReportBuilder\Model\EntityScheme\Provider
     */
    private $schemeProvider;

    /**
     * @var FilterInterface[]
     */
    private $filtersPool;

    public function __construct(
        Context $context,
        array $filtersPool = []
    ) {
        $this->columnResolver = $context->getColumnResolver();
        $this->simpleRelationResolver = $context->getSimpleRelationResolver();
        $this->schemeProvider = $context->getEntitySchemeProvider();
        $this->filtersPool = $filtersPool;
    }

    public function apply(string $filter, array $conditions): bool
    {
        $column = $this->resolveColumn($filter);
        if ($column === null) {
            return false;
        }

        if (!$this->getUseInternalFilterAggregation($column)) {
            return false;
        }

        $filter = $this->filtersPool[$column->getColumnType()];

        return $filter->apply($column, $conditions);
    }

    private function getUseInternalFilterAggregation(ColumnInterface $column): bool
    {
        return !$this->simpleRelationResolver->isEntitySimple($column->getEntityName())
            && $column->getAggregationType() === AggregationType::TYPE_NONE;
    }

    private function resolveColumn(string $filter): ?ColumnInterface
    {
        foreach ($this->columnResolver->resolve()->getAllColumns() as $columnId => $selectColumn) {
            if ($filter === $columnId || $filter === $selectColumn->getAlias()) {
                return $this->schemeProvider->getEntityScheme()->getColumnById($columnId);
            }
        }

        return null;
    }
}
