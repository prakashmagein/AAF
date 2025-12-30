<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Ui\DataProvider\Form\Report\Modifier\Columns;

use Amasty\ReportBuilder\Api\ColumnInterface;
use Amasty\ReportBuilder\Api\Data\ReportColumnInterface;
use Amasty\ReportBuilder\Model\EntityScheme\Column\AggregationType;
use Amasty\ReportBuilder\Model\EntityScheme\Provider;
use Amasty\ReportBuilder\Ui\DataProvider\Form\Report\Modifier\Columns;

class SchemeModifier implements ModifierInterface
{
    /**
     * @var Provider
     */
    private $schemeProvider;

    /**
     * @var AggregationType
     */
    private $aggregationType;

    public function __construct(Provider $schemeProvider, AggregationType $aggregationType)
    {
        $this->schemeProvider = $schemeProvider;
        $this->aggregationType = $aggregationType;
    }

    public function convert(ReportColumnInterface $reportColumn, array &$columnData): void
    {
        $schemeColumn = $this->schemeProvider->getEntityScheme()->getColumnById($reportColumn->getColumnId());
        if ($schemeColumn === null) {
            return;
        }
        $columnData = $schemeColumn->toArray();
        $columnData[ColumnInterface::ENTITY_NAME] = $schemeColumn->getEntityName();
        $columnData[Columns::COLUMN_DATA_AGGREGATION_OPTIONS] = $this->aggregationType->getOptionArray(
            $schemeColumn->getAvailableAggregationTypes()
        );
    }
}
