<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\ColumnBuilder\Simple;

use Amasty\ReportBuilder\Api\ColumnInterface;
use Amasty\ReportBuilder\Api\Data\SelectColumnInterface;
use Amasty\ReportBuilder\Model\ResourceModel\Report\Data\Select;
use Amasty\ReportBuilder\Model\SelectResolver\ColumnBuilder\RelationHelper;
use Amasty\ReportBuilder\Model\SelectResolver\ColumnResolverInterface;
use Amasty\ReportBuilder\Model\SelectResolver\EntitySimpleRelationResolver;
use Amasty\ReportBuilder\Model\SelectResolver\RelationResolverInterface;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\ColumnExpression;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class AddColumnToSelect
{
    /**
     * @var EntitySimpleRelationResolver
     */
    private $simpleRelationResolver;

    /**
     * @var RelationResolverInterface
     */
    private $relationResolver;

    /**
     * @var RelationHelper
     */
    private $relationHelper;

    /**
     * @var ColumnExpression
     */
    private $columnExpression;

    public function __construct(
        EntitySimpleRelationResolver $simpleRelationResolver,
        RelationResolverInterface $relationResolver,
        RelationHelper $relationHelper,
        ColumnExpression $columnExpression
    ) {
        $this->simpleRelationResolver = $simpleRelationResolver;
        $this->relationResolver = $relationResolver;
        $this->relationHelper = $relationHelper;
        $this->columnExpression = $columnExpression;
    }

    /**
     * @param Select $select
     * @param ColumnInterface $columnToJoin column from entity which we joined
     * @param SelectColumnInterface $selectColumn include params needed for select (alias , etc.)
     *
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(
        Select $select,
        ColumnInterface $columnToJoin,
        SelectColumnInterface $selectColumn
    ): void {
        $relations = $this->relationResolver->resolve();

        $entityName = $columnToJoin->getEntityName();
        if (!isset($relations[$entityName])) {
            return;
        }

        $alias = $selectColumn->getAlias();

        if ($this->simpleRelationResolver->isEntitySimple($entityName)) {
            $expression = $this->columnExpression->collectExpression($selectColumn);
            $select->columns([$alias => $expression]);
        } else {
            $relation = $relations[$entityName];

            if ($relation[RelationResolverInterface::TABLE] instanceof Select) {
                $this->addColumnThroughRelation(
                    $select,
                    $selectColumn,
                    $relation
                );
            } else {
                $parentRelation = $this->relationHelper->getParentSubSelectRelation($relation, $relations);
                if ($parentRelation) {
                    $this->addColumnThroughRelation(
                        $select,
                        $selectColumn,
                        $parentRelation
                    );
                } else {
                    $internalExpression = $this->columnExpression->collectInternalExpression($selectColumn);
                    $select->columns([$alias => $internalExpression]);
                }
            }
        }
    }

    private function addColumnThroughRelation($select, $selectColumn, $relation): void
    {
        $alias = $selectColumn->getAlias();
        $subSelect = $relation[RelationResolverInterface::TABLE];
        $internalExpression = $this->columnExpression->collectInternalExpression($selectColumn);

        $subSelect->columns([$alias => $internalExpression]);
        $this->relationHelper->throwRelations($select, $selectColumn, $relation);
    }
}
