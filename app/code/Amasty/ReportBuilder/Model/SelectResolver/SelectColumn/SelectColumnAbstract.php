<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\SelectColumn;

use Amasty\ReportBuilder\Api\Data\SelectColumnInterface;
use Magento\Framework\Api\AbstractSimpleObject;

abstract class SelectColumnAbstract extends AbstractSimpleObject implements SelectColumnInterface
{
    public function getAlias(): ?string
    {
        return $this->_get(self::ALIAS);
    }

    public function setAlias(?string $alias): void
    {
        $this->setData(self::ALIAS, $alias);
    }

    public function getExpression(): ?string
    {
        return $this->_get(self::EXPRESSION);
    }

    public function setExpression(?string $expression): void
    {
        $this->setData(self::EXPRESSION, $expression);
    }

    public function getExpressionInternal(): ?string
    {
        return $this->_get(self::EXPRESSION_INTERNAL);
    }

    public function setExpressionInternal(?string $expressionInternal): void
    {
        $this->setData(self::EXPRESSION_INTERNAL, $expressionInternal);
    }

    public function getEntityName(): ?string
    {
        return $this->_get(self::ENTITY_NAME);
    }

    public function setEntityName(?string $entityName): void
    {
        $this->setData(self::ENTITY_NAME, $entityName);
    }

    public function getAggregatedExpression(): ?string
    {
        return $this->_get(self::AGGREGATED_EXPRESSION);
    }

    public function setAggregatedExpression(?string $aggregatedExpression): void
    {
        $this->setData(self::AGGREGATED_EXPRESSION, $aggregatedExpression);
    }

    public function getExternalAggregatedExpression(): ?string
    {
        return $this->_get(self::EXTERNAL_AGGREGATED_EXPRESSION);
    }

    public function setExternalAggregatedExpression(?string $aggregatedExpression): void
    {
        $this->setData(self::EXTERNAL_AGGREGATED_EXPRESSION, $aggregatedExpression);
    }

    public function isUseAggregation(): bool
    {
        return (bool) $this->_get(self::USE_AGGREGATION);
    }

    public function setIsUseAggregation(bool $useAggregation): void
    {
        $this->setData(self::USE_AGGREGATION, $useAggregation);
    }

    public function getColumnId(): ?string
    {
        return $this->_get(self::COLUMN_ID);
    }

    public function setColumnId(string $columnId): void
    {
        $this->setData(self::COLUMN_ID, $columnId);
    }
}
