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
use Amasty\ReportBuilder\Model\EntityScheme\Column\ColumnType;
use Amasty\ReportBuilder\Model\ReportResolver;
use Amasty\ReportBuilder\Model\SelectResolver\EntitySimpleRelationResolver;

class AggregationUsageModifier implements ModifierInterface
{
    /**
     * @var ReportResolver
     */
    private $reportResolver;

    /**
     * @var EntitySimpleRelationResolver
     */
    private $simpleRelationResolver;

    public function __construct(
        ReportResolver $reportResolver,
        EntitySimpleRelationResolver $simpleRelationResolver
    ) {
        $this->reportResolver = $reportResolver;
        $this->simpleRelationResolver = $simpleRelationResolver;
    }

    public function modify(
        SelectColumnInterface $selectColumn,
        ?ColumnInterface $schemeColumn
    ): void {
        if ($schemeColumn === null) {
            return;
        }

        $useAggregation = $schemeColumn->getColumnType() === ColumnType::EAV_TYPE
            || !$this->simpleRelationResolver->isEntitySimple($selectColumn->getEntityName())
            || ($this->reportResolver->resolve()->getUsePeriod()
                && $selectColumn->getAggregatedExpression() !== AggregationType::EXPRESSION_NONE);

        $selectColumn->setIsUseAggregation($useAggregation);
    }
}
