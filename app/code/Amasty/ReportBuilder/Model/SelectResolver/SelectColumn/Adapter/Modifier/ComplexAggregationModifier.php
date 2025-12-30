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
use Amasty\ReportBuilder\Model\EntityScheme\Column\AggregationType;
use Amasty\ReportBuilder\Model\EntityScheme\Column\DataType;

class ComplexAggregationModifier implements ModifierInterface
{
    public function modify(
        SelectColumnInterface $selectColumn,
        ?ColumnInterface $schemeColumn
    ): void {
        if ($schemeColumn === null) {
            return;
        }

        $this->modifyDateExpression($schemeColumn, $selectColumn);
    }

    /**
     * @param ColumnInterface $schemeColumn
     * @param SelectColumnInterface $selectColumn
     */
    private function modifyDateExpression(ColumnInterface $schemeColumn, SelectColumnInterface $selectColumn): void
    {
        $dataType = $schemeColumn->getType();
        if (!\in_array($dataType, DataType::DATE_TYPES, true)) {
            return;
        }

        $aggregationType = $schemeColumn->getAggregationType();
        if ($aggregationType === AggregationType::TYPE_AVG) {
            $selectColumn->setAggregatedExpression('FROM_UNIXTIME(ROUND(AVG(UNIX_TIMESTAMP(%s))))');
        }
    }
}
