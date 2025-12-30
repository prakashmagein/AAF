<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Report\EntityDataModifiers;

use Amasty\ReportBuilder\Api\ColumnInterface;
use Amasty\ReportBuilder\Api\EntityInterface;

class ColumnsVisibilityModifier implements EntityDataModifierInterface
{
    public function modifyData(array $entityData): array
    {
        if (!empty($entityData[EntityInterface::COLUMNS])) {
            foreach ($entityData[EntityInterface::COLUMNS] as $key => $column) {
                $isHidden = $column[ColumnInterface::HIDDEN] ?? false;

                if ($isHidden) {
                    unset($entityData[EntityInterface::COLUMNS][$key]);
                }
            }
        }

        return $entityData;
    }
}
