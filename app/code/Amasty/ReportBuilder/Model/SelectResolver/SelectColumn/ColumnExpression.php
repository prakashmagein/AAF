<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\SelectColumn;

use Amasty\ReportBuilder\Api\Data\SelectColumnInterface;

/**
 * Column full expression resolver
 */
class ColumnExpression
{
    /**
     * Get column select expression for main Select.
     *
     * @param SelectColumnInterface $column
     *
     * @return string
     */
    public function collectExpression(SelectColumnInterface $column): string
    {
        if ($column->isUseAggregation()) {
            if ($column->getExpressionInternal()) {
                return sprintf($column->getExternalAggregatedExpression(), $column->getExpression());
            }
            
            return sprintf($column->getAggregatedExpression(), $column->getExpression());
        }

        return $column->getExpression();
    }

    /**
     * Get column select expression for Sub-select.
     *
     * @param SelectColumnInterface $column
     *
     * @return string
     */
    public function collectInternalExpression(SelectColumnInterface $column): string
    {
        if ($column->getExpressionInternal() !== null) {
            if ($column->isUseAggregation()) {
                return sprintf($column->getAggregatedExpression(), $column->getExpressionInternal());
            }

            return $column->getExpressionInternal();
        }

        return $this->collectExpression($column);
    }
}
