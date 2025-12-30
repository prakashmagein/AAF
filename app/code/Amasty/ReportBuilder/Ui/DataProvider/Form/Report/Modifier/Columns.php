<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Ui\DataProvider\Form\Report\Modifier;

use Amasty\ReportBuilder\Exception\NotExistColumnException;
use Amasty\ReportBuilder\Exception\NotExistTableException;
use Amasty\ReportBuilder\Model\Report\ColumnsResolver;
use Amasty\ReportBuilder\Model\ReportRegistry;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\ColumnBuilder;
use Amasty\ReportBuilder\Ui\DataProvider\Form\Report\Modifier\Columns\ModifierInterface as Extractor;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class Columns implements ModifierInterface
{
    public const COLUMNS_DATA_KEY = 'chosen_data';
    public const COLUMN_DATA_ID = 'id';
    public const COLUMN_DATA_ORDER = 'sortStatus';
    public const COLUMN_DATA_POSITION = 'position';
    public const COLUMN_DATA_FILTER = 'filtration';
    public const COLUMN_DATA_AGGREGATION = 'aggregation';
    public const COLUMN_DATA_VISIBILITY = 'isVisible';
    public const COLUMN_DATA_DATE_FILTER = 'isDate';
    public const COLUMN_DATA_AGGREGATION_OPTIONS = 'aggregationOptions';
    public const COLUMN_DATA_FILTER_IS_ACTIVE = 'isActive';
    public const COLUMN_DATA_FILTER_VALUE = 'value';
    public const COLUMN_DATA_CUSTOM_TITLE = 'customTitle';

    /**
     * @var ReportRegistry
     */
    private $reportRegistry;

    /**
     * @var ColumnsResolver
     */
    private $columnsResolver;

    /**
     * @var Extractor[]
     */
    private $extractors;

    /**
     * @var ColumnBuilder
     */
    private $columnBuilder;

    /**
     * @param ReportRegistry $reportRegistry
     * @param ColumnsResolver $columnsResolver
     * @param ColumnBuilder $columnBuilder
     * @param Extractor[] $extractors
     */
    public function __construct(
        ReportRegistry  $reportRegistry,
        ColumnsResolver $columnsResolver,
        ColumnBuilder $columnBuilder,
        array $extractors = []
    ) {
        $this->reportRegistry = $reportRegistry;
        $this->columnsResolver = $columnsResolver;
        $this->extractors = $extractors;
        $this->columnBuilder = $columnBuilder;
    }

    public function modifyData(array $data)
    {
        $report = $this->reportRegistry->getReport();
        $columns = [];

        foreach ($this->columnsResolver->getReportColumns() as $reportColumn) {
            $columnId = $reportColumn->getColumnId();

            try {
                $this->columnBuilder->validateColumn($reportColumn);
            } catch (NotExistColumnException | NotExistTableException $e) {
                continue;
            }

            $columnData = [];
            foreach ($this->extractors as $modifier) {
                $modifier->convert($reportColumn, $columnData);
            }
            if (!empty($columnData)) {
                $columns[$columnId] = $columnData;
            }
        }

        $data[$report->getReportId()][self::COLUMNS_DATA_KEY] = array_values($columns);

        return $data;
    }

    public function modifyMeta(array $meta)
    {
        return $meta;
    }
}
