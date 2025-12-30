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
use Amasty\ReportBuilder\Model\SelectResolver\ColumnBuilder\Eav\SubSelectRelationBuilder;
use Amasty\ReportBuilder\Model\SelectResolver\RelationBuilderInterface;
use Amasty\ReportBuilder\Model\SelectResolver\RelationResolverInterface;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\SelectEavColumn;

/**
 * EAV columns Select processor.
 */
class Eav implements BuilderInterface
{
    /**
     * @var RelationResolverInterface
     */
    private $relationResolver;

    /**
     * @var SubSelectRelationBuilder
     */
    private $subSelectRelationBuilder;

    /**
     * @var RelationHelper
     */
    private $relationHelper;

    public function __construct(
        RelationResolverInterface $relationResolver,
        SubSelectRelationBuilder $subSelectRelationBuilder,
        RelationHelper $relationHelper
    ) {
        $this->relationResolver = $relationResolver;
        $this->subSelectRelationBuilder = $subSelectRelationBuilder;
        $this->relationHelper = $relationHelper;
    }

    /**
     * Join EAV table and add column.
     *
     * @param Select $select
     * @param SelectColumnInterface|SelectEavColumn $selectColumn
     */
    public function build(Select $select, SelectColumnInterface $selectColumn): void
    {
        $relations = $this->relationResolver->resolve();
        $relation = [];

        if (isset($relations[$selectColumn->getEntityName()])) {
            $relation = $relations[$selectColumn->getEntityName()];
            $subSelect = $relation[RelationResolverInterface::TABLE];
            if (!$subSelect instanceof Select) {
                $relation = $this->relationHelper->getParentSubSelectRelation($relation, $relations);
                if ($relation) {
                    $subSelect = $relation[RelationResolverInterface::TABLE];
                } else {
                    $subSelect = $select;
                }
            }
        } else {
            $subSelect = $select;
        }

        $this->joinColumn($subSelect, $selectColumn);

        if ($relation) {
            $this->relationHelper->throwRelations($select, $selectColumn, $relation);
        }
    }

    /**
     * Resolve and join sub-select.
     */
    private function joinColumn(Select $select, SelectEavColumn $selectColumn): void
    {
        $relation = $this->subSelectRelationBuilder->build($selectColumn);

        $from = $select->getPart(Select::FROM);
        if (isset($from[$relation[RelationBuilderInterface::ALIAS]])) {
            return;
        }

        $select->joinByType(
            $relation[RelationBuilderInterface::TYPE],
            [$relation[RelationBuilderInterface::ALIAS] => $relation[RelationBuilderInterface::TABLE]],
            $relation[RelationBuilderInterface::CONDITION],
            $relation[RelationBuilderInterface::COLUMNS]
        );
    }
}
