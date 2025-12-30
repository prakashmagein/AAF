<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\ColumnBuilder;

use Amasty\ReportBuilder\Api\Data\SelectColumnInterface;
use Amasty\ReportBuilder\Model\ResourceModel\Report\Data\Select;
use Amasty\ReportBuilder\Model\SelectResolver\ColumnResolverInterface;
use Amasty\ReportBuilder\Model\SelectResolver\RelationResolverInterface;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\ColumnExpression;

class RelationHelper
{
    private const MAX_RELATION_ITERATION_COUNT = 50;

    /**
     * @var RelationResolverInterface
     */
    private $relationResolver;

    /**
     * @var ColumnExpression
     */
    private $columnExpression;

    public function __construct(
        RelationResolverInterface $relationResolver,
        ColumnExpression $columnExpression
    ) {
        $this->relationResolver = $relationResolver;
        $this->columnExpression = $columnExpression;
    }

    public function getParentSubSelectRelation(array $currentRelation, array $relations): ?array
    {
        $parentName = $currentRelation[RelationResolverInterface::PARENT];
        if (isset($relations[$parentName])) {
            $parent = $relations[$parentName];
            if ($parent[RelationResolverInterface::TABLE] instanceof Select) {
                return $parent;
            } else {
                return $this->getParentSubSelectRelation($parent, $relations);
            }
        }

        return null;
    }

    /**
     * Add column to all subselects on way to main select. And to main select.
     *
     * @param Select $select
     * @param SelectColumnInterface $selectColumn
     * @param array $relation
     */
    public function throwRelations(Select $select, SelectColumnInterface $selectColumn, array $relation): void
    {
        $expression = $this->columnExpression->collectExpression($selectColumn);
        $relations = $this->relationResolver->resolve();
        $alias = $selectColumn->getAlias();

        $i = 0;
        while ($i++ < self::MAX_RELATION_ITERATION_COUNT) {
            $previousRelation = $relation;

            $relation = $this->getParentSubSelectRelation($relation, $relations);
            if ($relation) {
                $subSelect = $relation[RelationResolverInterface::TABLE];
                $subSelect->columns(
                    [$alias => $expression],
                    $previousRelation[RelationResolverInterface::ALIAS]
                );
            } else {
                $select->columns(
                    [$alias => $expression],
                    $previousRelation[RelationResolverInterface::ALIAS]
                );
                break;
            }
        }
    }

    public function isColumnInSelect(Select $select, SelectColumnInterface $column): bool
    {
        $columns = $select->getPart(Select::COLUMNS);
        foreach ($columns as $columnData) {
            if (isset($columnData[2]) && $columnData[2] === $column->getAlias()) {
                return true;
            }
        }

        return false;
    }
}
