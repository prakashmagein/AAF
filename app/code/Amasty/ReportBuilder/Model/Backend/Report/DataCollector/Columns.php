<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Backend\Report\DataCollector;

use Amasty\ReportBuilder\Api\Data\ReportColumnInterface;
use Amasty\ReportBuilder\Api\Data\ReportInterface;
use Amasty\ReportBuilder\Model\Backend\Report\DataCollector\Columns\FilterCollector;
use Amasty\ReportBuilder\Model\Backend\Report\DataCollectorInterface;
use Amasty\ReportBuilder\Model\EntityScheme\Column\AggregationType;
use Amasty\ReportBuilder\Model\Report\Column;
use Amasty\ReportBuilder\Model\Report\ColumnFactory;
use Amasty\ReportBuilder\Model\Report\ColumnRegistry;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;

class Columns implements DataCollectorInterface
{
    public const COLUMNS_DATA_KEY = 'chosen_data';
    public const COLUMN_DATA_ID = 'id';
    public const COLUMN_DATA_POSITION = 'position';
    public const COLUMN_DATA_ORDER = 'sortStatus';
    public const COLUMN_DATA_FILTER = 'filtration';
    public const COLUMN_DATA_VISIBILITY = 'isVisible';
    public const COLUMN_DATA_DATE_FILTER = 'isDate';
    public const COLUMN_DATA_AGGREGATION = 'aggregation';
    public const COLUMN_DATA_CUSTOM_TITLE = 'customTitle';
    public const ACTIVE_KEY = 'isActive';
    public const VALUE_KEY = 'value';

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @var FilterCollector
     */
    private $filterCollector;

    /**
     * @var ColumnRegistry
     */
    private $columnRegistry;

    /**
     * @var ColumnFactory
     */
    private $columnModelFactory;

    /**
     * @var Columns\ColumnDataCollectorInterface[]
     */
    private $columnDataCollectors;

    public function __construct(
        Json $serializer,
        FilterCollector $filterCollector,
        ColumnRegistry $columnRegistry,
        ColumnFactory $columnModelFactory,
        array $columnDataCollectors = []
    ) {
        $this->serializer = $serializer;
        $this->filterCollector = $filterCollector;
        $this->columnRegistry = $columnRegistry;
        $this->columnModelFactory = $columnModelFactory;
        $this->columnDataCollectors = $columnDataCollectors;
    }

    /**
     * @param ReportInterface $report
     * @param array $inputData
     *
     * @return array
     * @throws LocalizedException
     */
    public function collect(ReportInterface $report, array $inputData): array
    {
        if (!isset($inputData[self::COLUMNS_DATA_KEY])) {
            return [];
        }

        try {
            $columnsData = $this->serializer->unserialize($inputData[self::COLUMNS_DATA_KEY]);
        } catch (\InvalidArgumentException $e) {
            throw new LocalizedException(__('The problem occurred while parsing column\'s json'), $e);
        }

        return $this->prepareColumns($report, $columnsData);
    }

    private function prepareColumns(ReportInterface $report, array $columnsData): array
    {
        $result = [];
        $reportId = $report->getReportId();
        $oldColumns = $this->columnRegistry->get($reportId);
        $this->columnRegistry->unset($reportId);
        
        foreach ($columnsData as $columnData) {
            if (!isset($columnData[self::COLUMN_DATA_ID])) {
                continue;
            }
            $columnId = (string) $columnData[self::COLUMN_DATA_ID];
            $column = $oldColumns[$columnId] ?? null;

            if ($column === null) {
                /** @var Column $column */
                $column = $this->columnModelFactory->create();
                $column->setColumnId($columnId);
            }
            $this->columnRegistry->addItem($reportId, $column);
            if (isset($columnData[self::COLUMN_DATA_DATE_FILTER])) {
                $column->setIsDateFilter((bool)$columnData[self::COLUMN_DATA_DATE_FILTER]);
            }
            $column->setAggregationType($this->resolveAggregationType($columnData));
            if (isset($columnData[self::COLUMN_DATA_ORDER])) {
                $column->setOrder((int)$columnData[self::COLUMN_DATA_ORDER]);
            }

            if (isset($columnData[self::COLUMN_DATA_VISIBILITY])) {
                $column->setVisibility((bool)$columnData[self::COLUMN_DATA_VISIBILITY]);
            }
            if (isset($columnData[self::COLUMN_DATA_POSITION])) {
                $column->setPosition((int)$columnData[self::COLUMN_DATA_POSITION]);
            }
            if (isset($columnData[self::COLUMN_DATA_CUSTOM_TITLE])) {
                $column->setCustomTitle((string)$columnData[self::COLUMN_DATA_CUSTOM_TITLE]);
            }

            $this->collectAdditionalColumnData($column, $columnData);

            if (!$column->isDeleted()) {
                $result[$columnId] = $column->toArray();
            }
        }

        return [ReportInterface::COLUMNS => $result];
    }

    private function collectAdditionalColumnData(ReportColumnInterface $column, array $columnData): void
    {
        foreach ($this->columnDataCollectors as $collector) {
            $collector->collectData($column, $columnData);
        }
    }

    private function resolveAggregationType(array $columnData): ?string
    {
        if (isset($columnData[self::COLUMN_DATA_AGGREGATION][self::ACTIVE_KEY])
            && $columnData[self::COLUMN_DATA_AGGREGATION][self::ACTIVE_KEY]
        ) {
            return $columnData[self::COLUMN_DATA_AGGREGATION][self::VALUE_KEY]
                ?? AggregationType::DEFAULT_AGGREGATION_TYPE;
        }

        return null;
    }
}
