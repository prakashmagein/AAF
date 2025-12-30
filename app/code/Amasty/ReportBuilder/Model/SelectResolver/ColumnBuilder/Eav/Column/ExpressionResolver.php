<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\ColumnBuilder\Eav\Column;

use Amasty\ReportBuilder\Model\SelectResolver\RelationBuilder;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\SelectEavColumn;

class ExpressionResolver
{
    public function resolve(SelectEavColumn $selectColumn, array $relations): array
    {
        $expression = '';

        foreach ($relations as $relation) {
            if ($expression) {
                $expression = sprintf(
                    'IFNULL(%s, %s)',
                    $relation[RelationBuilder::COLUMNS],
                    $expression
                );
            } else {
                $expression = $relation[RelationBuilder::COLUMNS];
            }
        }

        if ($selectColumn->isUseAggregation()) {
            $expression = sprintf(
                $selectColumn->getAggregatedExpression(),
                $expression
            );
        }

        return [$selectColumn->getAlias() => $expression];
    }
}
