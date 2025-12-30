<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Report;

use Amasty\ReportBuilder\Model\Report\EntityDataModifiers\EntityDataModifierInterface;

class EntitiesDataModifierComposite implements EntitiesDataModifierInterface
{
    /**
     * @var EntityDataModifierInterface[]
     */
    private $modifiers;

    public function __construct(
        array $modifiers = []
    ) {
        $this->modifiers = $modifiers;
    }

    public function modify(array $entitiesData): array
    {
        foreach ($entitiesData as $key => $entityData) {
            foreach ($this->modifiers as $modifier) {
                if ($modifier instanceof EntityDataModifierInterface && is_array($entityData)) {
                    $entityData = $modifier->modifyData($entityData);
                }
            }

            $entitiesData[$key] = $entityData;
        }

        return $entitiesData;
    }
}
