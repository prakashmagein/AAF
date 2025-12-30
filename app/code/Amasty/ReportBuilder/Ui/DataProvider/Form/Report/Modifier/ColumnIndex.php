<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Ui\DataProvider\Form\Report\Modifier;

use Amasty\ReportBuilder\Api\ColumnInterface;
use Amasty\ReportBuilder\Model\EntityScheme\Provider;
use Amasty\ReportBuilder\Model\ReportRegistry;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class ColumnIndex implements ModifierInterface
{
    public const COLUMN_DATA_INDEX = 'index';

    /**
     * @var ReportRegistry
     */
    private $reportRegistry;

    /**
     * @var Provider
     */
    private $schemeProvider;

    public function __construct(
        ReportRegistry $reportRegistry,
        Provider $schemeProvider
    ) {
        $this->reportRegistry = $reportRegistry;
        $this->schemeProvider = $schemeProvider;
    }

    public function modifyData(array $data)
    {
        $report = $this->reportRegistry->getReport();
        $scheme = $this->schemeProvider->getEntityScheme();

        if (!isset($data[$report->getReportId()][Columns::COLUMNS_DATA_KEY])) {
            return $data;
        }

        foreach ($data[$report->getReportId()][Columns::COLUMNS_DATA_KEY] as &$column) {
            if (isset($column[ColumnInterface::ENTITY_NAME])) {
                $column[self::COLUMN_DATA_INDEX] = $scheme->getEntityByName($column[ColumnInterface::ENTITY_NAME])
                    ->getColumnIndex($column[ColumnInterface::NAME]);
            }
        }

        return $data;
    }

    public function modifyMeta(array $meta)
    {
        return $meta;
    }
}
