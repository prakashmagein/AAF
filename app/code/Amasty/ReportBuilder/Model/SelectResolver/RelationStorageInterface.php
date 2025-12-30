<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver;

interface RelationStorageInterface
{
    public function init(): void;

    public function addRelation(array $relationConfig): void;

    public function getAllRelations(): array;

    public function setRelations(array $relations): void;
}
