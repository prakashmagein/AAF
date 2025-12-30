<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\SelectColumn;

class ColumnExpressionResolver implements ColumnExpressionResolverInterface
{
    /**
     * @var ColumnResolverInterface
     */
    private $columnResolver;

    /**
     * @var ColumnExpression
     */
    private $columnExpression;

    public function __construct(
        ColumnResolverInterface $columnResolver,
        ColumnExpression $columnExpression
    ) {
        $this->columnResolver = $columnResolver;
        $this->columnExpression = $columnExpression;
    }

    public function resolve(string $columnAlias, bool $useInternal = false): string
    {
        $column = $this->columnResolver->resolve()->getColumnByAlias($columnAlias);

        if ($column !== null) {
            if ($useInternal) {
                return $this->columnExpression->collectInternalExpression($column);
            }

            return $this->columnExpression->collectExpression($column);
        }

        return $columnAlias;
    }
}
