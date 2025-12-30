<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Report;

use Amasty\ReportBuilder\Api\ColumnInterface;
use Amasty\ReportBuilder\Model\ReportResolver;
use Amasty\ReportBuilder\Model\ResourceModel\Report\Column\Collection;
use Magento\Framework\DB\Select;

class ColumnsResolver
{
    /**
     * @var ColumnProvider
     */
    private $columnProvider;

    /**
     * @var ReportResolver
     */
    private $reportResolver;

    /**
     * @param ColumnProvider $columnProvider
     * @param ReportResolver $reportResolver
     */
    public function __construct(ColumnProvider $columnProvider, ReportResolver $reportResolver)
    {
        $this->columnProvider = $columnProvider;
        $this->reportResolver = $reportResolver;
    }

    /**
     * @return \Amasty\ReportBuilder\Api\Data\ReportColumnInterface[]
     */
    public function getReportColumns(): array
    {
        return $this->columnProvider->getColumnsByReportId($this->getReportId());
    }

    /**
     * @return string[]
     */
    public function getReportColumnIds(): iterable
    {
        foreach ($this->getReportColumns() as $column) {
            yield $column->getColumnId();
        }
    }

    /**
     * @return string|null
     */
    public function getSortingColumnId(): ?string
    {
        $column = $this->columnProvider->getReportSortingColumn($this->getReportId());

        return $column ? $column->getColumnId() : null;
    }

    /**
     * @return string
     */
    public function getSortingColumnExpression(): string
    {
        $column = $this->columnProvider->getReportSortingColumn($this->getReportId());
        $expression = Select::SQL_DESC;

        if ($column && $column->getOrder() === ColumnInterface::ORDER_ASC) {
            $expression = Select::SQL_ASC;
        }

        return $expression;
    }

    /**
     * @return int
     */
    private function getReportId(): int
    {
        return $this->reportResolver->resolve()->getReportId();
    }
}
