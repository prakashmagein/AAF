<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\ColumnFilter\ConditionPreprocessor;

use Amasty\ReportBuilder\Model\SelectResolver\ColumnFilter\FilterColumn;

class DropdownPreprocessor implements PreprocessorInterface
{
    /**
     * @var FilterColumn
     */
    private $filterColumn;

    public function __construct(FilterColumn $filterColumn)
    {
        $this->filterColumn = $filterColumn;
    }

    public function process(string $filter, array $conditions): ?string
    {
        $column = $this->filterColumn->getEntityByFilterAlias($filter);
        if ($column === null || !in_array($column->getFrontendModel(), ['select', 'multiselect'], true)) {
            return null;
        }

        if ($this->filterColumn->isFilterCanUseWhere($filter)) {
            return null;
        }

        $havingConditions = [];
        foreach ($conditions as $condition) {
            $havingConditions[] = sprintf(
                'FIND_IN_SET("%s", %s)',
                $condition,
                $filter
            );
        }

        return implode(' OR ', $havingConditions);
    }
}
