<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Api\Data;

interface SelectColumnInterface
{
    /**
     * String constants for property names
     */

    /**
     * Column alias.
     * Can be used as column alias in sub select and main select.
     * Can be used in filter, order
     */
    public const ALIAS = 'alias';

    /**
     * Column name for main select.
     * For full expression use with AGGREGATED_EXPRESSION
     */
    public const EXPRESSION = 'expression';

    /**
     * Column name for sub select.
     * For full expression use with AGGREGATED_EXPRESSION
     */
    public const EXPRESSION_INTERNAL = 'expression_internal';

    /**
     * Can be used as table alias
     */
    public const ENTITY_NAME = 'entity_name';

    /**
     * Aggregation expression template
     */
    public const AGGREGATED_EXPRESSION = 'aggregated_expression';

    /**
     * Aggregation expression template for line of sub-selects
     */
    public const EXTERNAL_AGGREGATED_EXPRESSION = 'ex_aggregated_expression';

    /**
     * Is column have sub select
     */
    public const USE_AGGREGATION = 'use_aggregation';

    /**
     * Can be used as column name in sub select
     */
    public const COLUMN_ID = 'column_id';

    /**
     * Column type
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Getter for Alias.
     *
     * @return string|null
     */
    public function getAlias(): ?string;

    /**
     * Setter for Alias.
     *
     * @param string|null $alias
     *
     * @return void
     */
    public function setAlias(?string $alias): void;

    /**
     * Getter for Expression.
     *
     * @return string|null
     */
    public function getExpression(): ?string;

    /**
     * Setter for Expression.
     *
     * @param string|null $expression
     *
     * @return void
     */
    public function setExpression(?string $expression): void;

    /**
     * Getter for ExpressionInternal.
     *
     * @return string|null
     */
    public function getExpressionInternal(): ?string;

    /**
     * Setter for ExpressionInternal.
     *
     * @param string|null $expressionInternal
     *
     * @return void
     */
    public function setExpressionInternal(?string $expressionInternal): void;

    /**
     * Getter for EntityName.
     *
     * @return string|null
     */
    public function getEntityName(): ?string;

    /**
     * Setter for EntityName.
     *
     * @param string|null $entityName
     *
     * @return void
     */
    public function setEntityName(?string $entityName): void;

    /**
     * Getter for AggregatedExpression.
     *
     * @return string|null
     */
    public function getAggregatedExpression(): ?string;

    /**
     * Setter for AggregatedExpression.
     *
     * @param string|null $aggregatedExpression
     *
     * @return void
     */
    public function setAggregatedExpression(?string $aggregatedExpression): void;

    /**
     * Getter for ExternalAggregatedExpression.
     *
     * @return string|null
     */
    public function getExternalAggregatedExpression(): ?string;

    /**
     * Setter for ExternalAggregatedExpression.
     *
     * @param string|null $aggregatedExpression
     *
     * @return void
     */
    public function setExternalAggregatedExpression(?string $aggregatedExpression): void;

    /**
     * Getter for UseAggregation.
     *
     * @return bool
     */
    public function isUseAggregation(): bool;

    /**
     * Setter for UseAggregation.
     *
     * @param bool $useAggregation
     *
     * @return void
     */
    public function setIsUseAggregation(bool $useAggregation): void;

    /**
     * Column id like 'order.entity_id'
     *
     * @return string
     */
    public function getColumnId(): ?string;

    /**
     * @param string $columnId
     */
    public function setColumnId(string $columnId): void;
}
