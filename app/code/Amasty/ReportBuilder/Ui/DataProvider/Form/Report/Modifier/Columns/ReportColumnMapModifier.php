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
use Amasty\ReportBuilder\Model\EntityScheme\Column\FilterConditionType;
use Amasty\ReportBuilder\Ui\DataProvider\Form\Report\Modifier\Columns;
use Magento\Framework\Serialize\Serializer\Json;

class ReportColumnMapModifier implements ModifierInterface
{
    /**
     * @var Json
     */
    private $jsonSerializer;

    public function __construct(Json $jsonSerializer)
    {
        $this->jsonSerializer = $jsonSerializer;
    }

    public function convert(ReportColumnInterface $reportColumn, array &$columnData): void
    {
        $columnId = $reportColumn->getColumnId();
        $columnData[ColumnInterface::ID] = $columnId;
        $columnData[Columns::COLUMN_DATA_DATE_FILTER] = (bool)$reportColumn->getIsDateFilter();
        $columnData[Columns::COLUMN_DATA_ORDER] = (int)$reportColumn->getOrder();
        $columnData[Columns::COLUMN_DATA_VISIBILITY] = (bool)$reportColumn->getVisibility();
        $columnData[Columns::COLUMN_DATA_POSITION] = (int) $reportColumn->getPosition();
        $columnData[Columns::COLUMN_DATA_CUSTOM_TITLE] = (string) $reportColumn->getCustomTitle();

        $columnData[Columns::COLUMN_DATA_FILTER] = $this->getFilterData($reportColumn);
        $columnData[Columns::COLUMN_DATA_AGGREGATION] = $this->getAggregationData($reportColumn);
    }

    /**
     * @param ReportColumnInterface $reportColumn
     *
     * @return array
     */
    private function getFilterData(ReportColumnInterface $reportColumn): array
    {
        $filterValue = '';
        $hasFilter = !empty($reportColumn->getFilter());

        if ($hasFilter) {
            $value = $this->jsonSerializer->unserialize($reportColumn->getFilter());
            $filterValue = $value[FilterConditionType::CONDITION_VALUE] ?? $value;
        }

        return [
            Columns::COLUMN_DATA_FILTER_IS_ACTIVE => $hasFilter,
            Columns::COLUMN_DATA_FILTER_VALUE => $filterValue
        ];
    }

    /**
     * @param ReportColumnInterface $reportColumn
     *
     * @return array
     */
    private function getAggregationData(ReportColumnInterface $reportColumn): array
    {
        return [
            Columns::COLUMN_DATA_FILTER_IS_ACTIVE => !empty($reportColumn->getAggregationType()),
            Columns::COLUMN_DATA_FILTER_VALUE => $reportColumn->getAggregationType()
        ];
    }
}
