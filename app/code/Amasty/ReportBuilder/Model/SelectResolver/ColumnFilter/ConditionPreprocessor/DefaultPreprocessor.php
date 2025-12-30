<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\ColumnFilter\ConditionPreprocessor;

use Amasty\ReportBuilder\Model\ResourceModel\Report;
use Amasty\ReportBuilder\Model\SelectResolver\ColumnFilter\FilterColumn;

class DefaultPreprocessor implements PreprocessorInterface
{
    /**
     * @var Report
     */
    private $reportResource;

    /**
     * @var FilterColumn
     */
    private $filterColumn;

    public function __construct(Report $reportResource, FilterColumn $filterColumn)
    {
        $this->reportResource = $reportResource;
        $this->filterColumn = $filterColumn;
    }

    public function process(string $filter, array $conditions): ?string
    {
        $connection = $this->reportResource->getConnection();
        $whereConditions = [];
        foreach ($conditions as $key => $condition) {
            $whereConditions[] = $connection->prepareSqlCondition(
                $this->getAlias($filter),
                [$key => $condition]
            );
        }

        return implode(' AND ', $whereConditions);
    }

    private function getAlias(string $filter): string
    {
        $selectColumn = $this->filterColumn->getSelectColumnByFilter($filter);
        return $this->filterColumn->isFilterCanUseWhere($filter)
            ? $selectColumn->getExpression()
            : $selectColumn->getAlias();
    }
}
