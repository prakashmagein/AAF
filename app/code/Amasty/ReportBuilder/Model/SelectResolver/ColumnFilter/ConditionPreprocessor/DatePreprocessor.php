<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\ColumnFilter\ConditionPreprocessor;

use Amasty\ReportBuilder\Model\EntityScheme\Provider;
use Amasty\ReportBuilder\Model\ReportResolver;
use Amasty\ReportBuilder\Model\ResourceModel\Report;
use Amasty\ReportBuilder\Model\SelectResolver\ColumnFilter\FilterColumn;
use Amasty\ReportBuilder\Model\SelectResolver\MainTableBuilder\IntervalProvider;
use Amasty\ReportBuilder\Model\View\FiltersProvider;
use Magento\Framework\DB\Sql\ColumnValueExpression;
use Magento\Framework\DB\Sql\ColumnValueExpressionFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Convert date filters condition from current config timezone to UTC timezone.
 * Wrap interval if column filter use interval.
 */
class DatePreprocessor implements PreprocessorInterface
{
    /**
     * @var FilterColumn
     */
    private $filterColumn;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var Report
     */
    private $reportResource;

    /**
     * @var ReportResolver
     */
    private $reportResolver;

    /**
     * @var Provider
     */
    private $schemeProvider;

    /**
     * @var FiltersProvider
     */
    private $filtersProvider;

    /**
     * @var ColumnValueExpressionFactory
     */
    private $expressionFactory;

    /**
     * @var IntervalProvider
     */
    private $intervalProvider;

    public function __construct(
        FilterColumn $filterColumn,
        TimezoneInterface $timezone,
        Report $reportResource,
        ReportResolver $reportResolver,
        Provider $schemeProvider,
        FiltersProvider $filtersProvider,
        ColumnValueExpressionFactory $expressionFactory,
        IntervalProvider $intervalProvider
    ) {
        $this->filterColumn = $filterColumn;
        $this->timezone = $timezone;
        $this->reportResource = $reportResource;
        $this->reportResolver = $reportResolver;
        $this->schemeProvider = $schemeProvider;
        $this->filtersProvider = $filtersProvider;
        $this->expressionFactory = $expressionFactory;
        $this->intervalProvider = $intervalProvider;
    }

    public function process(string $filter, array $conditions): ?string
    {
        $column = $this->filterColumn->getEntityByFilterAlias($filter);
        if ($column === null || $column->getFrontendModel() !== 'dateRange') {
            return null;
        }

        $connection = $this->reportResource->getConnection();
        $whereConditions = [];
        foreach ($conditions as $key => $condition) {
            if (is_object($condition)) {
                // need because object can be changed in convertConfigTimeToUtc
                // second call convertConfigTimeToUtc can trigger error
                $condition = clone $condition;
            }
            $condition = $this->timezone->convertConfigTimeToUtc($condition);
            if ($this->isPeriodColumn($column->getColumnId())) {
                $condition = $this->convertToPeriodInterval($condition);
            }
            $whereConditions[] = $connection->prepareSqlCondition($filter, [$key => $condition]);
        }

        return implode(' AND ', $whereConditions);
    }

    private function convertToPeriodInterval(string $condition): ColumnValueExpression
    {
        $condition = $this->intervalProvider->getInterval(
            $this->reportResource->getConnection()->quote($condition),
            $this->filtersProvider->getInterval()
        );
        return $this->expressionFactory->create(['expression' => array_shift($condition)]);
    }

    private function isPeriodColumn(string $columnId): bool
    {
        $report = $this->reportResolver->resolve();

        if (!$report->getUsePeriod()) {
            return false;
        }

        $periodColumnId = $this->schemeProvider->getEntityScheme()
            ->getEntityByName($report->getMainEntity())
            ->getPeriodColumn()
            ->getColumnId();

        return $periodColumnId === $columnId;
    }
}
