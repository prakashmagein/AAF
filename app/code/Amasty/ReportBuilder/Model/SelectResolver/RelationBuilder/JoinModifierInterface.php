<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\RelationBuilder;

interface JoinModifierInterface
{
    public function modify(array $relations): array;

    public function isValid(array $relation): bool;
}
