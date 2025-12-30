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
use Amasty\ReportBuilder\Model\SelectResolver\ColumnResolverInterface;

class SimpleAggregationModifier implements ModifierInterface
{
    /**
     * @var AggregationType
     */
    private $aggregationType;

    public function __construct(
        AggregationType $aggregationType
    ) {
        $this->aggregationType = $aggregationType;
    }

    public function modify(
        SelectColumnInterface $selectColumn,
        ?ColumnInterface $schemeColumn
    ): void {
        if ($schemeColumn === null) {
            return;
        }

        $simpleAggregationsTypes = $this->aggregationType->getSimpleAggregationsType();
        $aggregationType = $schemeColumn->getAggregationType();

        if (array_key_exists($aggregationType, $simpleAggregationsTypes)) {
            $selectColumn->setAggregatedExpression($simpleAggregationsTypes[$aggregationType]);
            $aggregatedExpression = $this->aggregationType->getParentAggregationExpression($schemeColumn);
            $selectColumn->setExternalAggregatedExpression($aggregatedExpression);
        }
    }
}
