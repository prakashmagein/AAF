<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\MainTableBuilder;

use Amasty\ReportBuilder\Model\EntityScheme\Column\ColumnType;
use Amasty\ReportBuilder\Model\EntityScheme\Provider;
use Amasty\ReportBuilder\Model\ReportResolver;
use Amasty\ReportBuilder\Model\SelectResolver\Context;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\ColumnExpressionResolverInterface;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\ColumnResolverInterface;

class MainEntityColumnProvider
{
    /**
     * @var IntervalProvider
     */
    private $intervalProvider;

    /**
     * @var ColumnResolverInterface
     */
    private $columnResolver;

    /**
     * @var ColumnExpressionResolverInterface
     */
    private $columnExpressionResolver;

    /**
     * @var Provider
     */
    private $provider;

    /**
     * @var ReportResolver
     */
    private $reportResolver;

    public function __construct(
        Context $context,
        IntervalProvider $intervalProvider
    ) {
        $this->intervalProvider = $intervalProvider;
        $this->columnResolver = $context->getColumnResolver();
        $this->columnExpressionResolver = $context->getColumnExpressionResolver();
        $this->provider = $context->getEntitySchemeProvider();
        $this->reportResolver = $context->getReportResolver();
    }

    public function getColumns(?string $interval = null): array
    {
        $columns = [];
        $report = $this->reportResolver->resolve();

        $columnName = $this->getColumnName();
        $alias = $report->getMainEntity() . '_' . $columnName;

        if ($report->getUsePeriod() && $interval) {
            [$expression, $groups] = $this->intervalProvider->getInterval(
                sprintf('%s.%s', $report->getMainEntity(), $columnName),
                $interval
            );
            $columns[$alias] = new \Zend_Db_Expr($expression);
        } else {
            $expression = sprintf('%s.%s', $report->getMainEntity(), $columnName);
            $columns[$alias] = $expression;
            $groups = [$expression];
        }

        return [$this->getAllColumns($columns), $groups];
    }

    private function getColumnName(): string
    {
        $report = $this->reportResolver->resolve();
        $scheme = $this->provider->getEntityScheme();
        $entity = $scheme->getEntityByName($report->getMainEntity());

        return $report->getUsePeriod() ? $entity->getPeriodColumn()->getName() : $entity->getPrimaryColumn()->getName();
    }

    private function getAllColumns(array $columns): array
    {
        $selectColumnRegistry = $this->columnResolver->resolve();
        $scheme = $this->provider->getEntityScheme();
        $report = $this->reportResolver->resolve();

        foreach ($selectColumnRegistry->getAllColumns() as $selectColumn) {
            $column = $scheme->getColumnById($selectColumn->getColumnId());
            if ($column === null) {
                continue;
            }

            $alias = $selectColumn->getAlias();
            if ($column->getEntityName() === $report->getMainEntity()
                && !isset($columns[$alias])
                && !in_array($column->getColumnType(), [ColumnType::EAV_TYPE, ColumnType::FOREIGN_TYPE], true)
            ) {
                $columns[$alias] = $this->columnExpressionResolver->resolve(
                    $alias,
                    true
                );
            }
        }

        return $columns;
    }
}
