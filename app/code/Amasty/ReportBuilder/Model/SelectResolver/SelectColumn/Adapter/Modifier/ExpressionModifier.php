<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\Adapter\Modifier;

use Amasty\ReportBuilder\Api\ColumnInterface;
use Amasty\ReportBuilder\Api\Data\SelectColumnInterface;
use Amasty\ReportBuilder\Model\SelectResolver\EntitySimpleRelationResolver;

class ExpressionModifier implements ModifierInterface
{
    /**
     * @var ModifierInterface[]
     */
    private $customModifiers;

    /**
     * @var EntitySimpleRelationResolver
     */
    private $simpleRelationResolver;

    public function __construct(
        EntitySimpleRelationResolver $simpleRelationResolver,
        array $customModifiers = []
    ) {
        $this->customModifiers = $customModifiers;
        $this->simpleRelationResolver = $simpleRelationResolver;
    }

    public function modify(
        SelectColumnInterface $selectColumn,
        ?ColumnInterface $schemeColumn
    ): void {
        if ($schemeColumn) {
            $customModifier = $this->customModifiers[$schemeColumn->getCustomExpression()] ?? null;
            if ($customModifier) {
                $customModifier->modify($selectColumn, $schemeColumn);
            }
        }

        if ($this->simpleRelationResolver->isEntitySimple($selectColumn->getEntityName())) {
            $this->prepareExpressionForMainSelect($selectColumn);
        } else {
            $this->prepareExpressionForSubSelect($selectColumn);
        }
    }

    /**
     * @param SelectColumnInterface $selectColumn
     */
    private function prepareExpressionForSubSelect(SelectColumnInterface $selectColumn): void
    {
        $selectColumn->setExpression($selectColumn->getAlias());

        if ($selectColumn->getExpressionInternal() === null) {
            $selectColumn->setExpressionInternal($selectColumn->getColumnId());
        }
    }

    /**
     * @param SelectColumnInterface $selectColumn
     */
    private function prepareExpressionForMainSelect(SelectColumnInterface $selectColumn): void
    {
        if ($selectColumn->getExpression() === null) {
            $selectColumn->setExpression($selectColumn->getColumnId());
        }
    }
}
