<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver;

use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\ColumnModifiers;

class RelationStorage implements RelationStorageInterface
{
    /**
     * @var array
     */
    private $relations = [];

    public function init(): void
    {
        $this->relations = [];
    }

    public function addRelation(array $relationConfig): void
    {
        $this->relations[$relationConfig[RelationResolverInterface::ALIAS]] = $relationConfig;
    }

    public function getAllRelations(): array
    {
        return $this->relations;
    }

    public function setRelations(array $relations): void
    {
        foreach ($relations as $relation) {
            $this->addRelation($relation);
        }
    }
}
