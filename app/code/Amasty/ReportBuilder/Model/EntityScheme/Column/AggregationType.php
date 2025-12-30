<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\EntityScheme\Column;

use Amasty\ReportBuilder\Api\ColumnInterface;

class AggregationType
{
    public const TYPE_NONE = 'none';
    public const TYPE_MIN = 'min';
    public const TYPE_MAX = 'max';
    public const TYPE_AVG = 'avg';
    public const TYPE_SUM = 'sum';
    public const TYPE_COUNT = 'count';
    public const TYPE_GROUP_CONCAT = 'group_concat';

    public const DEFAULT_AGGREGATION_TYPE = 'none';

    public const EXPRESSION_NONE = '%s';

    public function getSimpleAggregationsType(): array
    {
        return [
            self::TYPE_NONE => self::EXPRESSION_NONE,
            self::TYPE_MIN => 'MIN(%s)',
            self::TYPE_MAX => 'MAX(%s)',
            self::TYPE_AVG => 'AVG(%s)',
            self::TYPE_SUM => 'SUM(%s)',
            self::TYPE_COUNT => 'COUNT(DISTINCT %s)',
            self::TYPE_GROUP_CONCAT => 'GROUP_CONCAT(DISTINCT IF(%1$s = "", NULL, %1$s) separator ",")',
        ];
    }

    /**
     * Check if expression correct for requested type
     *
     * @param string $expression
     * @param string $requestedType
     * @return bool
     */
    public function checkTypeExpression(string $expression, string $requestedType): bool
    {
        $aggregations = $this->getSimpleAggregationsType();

        return isset($aggregations[$requestedType]) && $aggregations[$requestedType] == $expression;
    }

    /**
     * Retrieve aggregation type for main select
     *
     * @param ColumnInterface $column
     *
     * @return string
     */
    public function getParentAggregationExpression(ColumnInterface $column): string
    {
        $aggregationType = $column->getAggregationType();

        if ($aggregationType === self::TYPE_AVG && \in_array($column->getType(), DataType::DATE_TYPES, true)
        ) {
            return 'FROM_UNIXTIME(ROUND(AVG(UNIX_TIMESTAMP(%s))))';
        }
        
        return $this->getParentAggregationByType($aggregationType);
    }

    public function getParentAggregationByType(string $aggregationType): string
    {
        if ($aggregationType === self::TYPE_COUNT) {
            $aggregationType = self::TYPE_SUM;
        }

        $types = $this->getSimpleAggregationsType();

        return $types[$aggregationType];
    }

    /**
     * @param array $include
     * @return array
     */
    public function getOptionArray(array $include = []): array
    {
        $options = [
            self::TYPE_NONE => __('Default'),
            self::TYPE_MIN => __('Min Value'),
            self::TYPE_MAX => __('Max Value'),
            self::TYPE_AVG => __('Average'),
            self::TYPE_SUM => __('Sum'),
            self::TYPE_COUNT => __('Count'),
            self::TYPE_GROUP_CONCAT => __('Group Concat'),
        ];

        $optionArray = [];
        foreach ($include as $optionValue) {
            if (isset($options[$optionValue])) {
                $optionArray[] = [
                    'value' => $optionValue,
                    'label' => $options[$optionValue]
                ];
            }
        }

        return $optionArray;
    }
}
