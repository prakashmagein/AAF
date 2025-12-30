<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Setup\Schema\Triggers;

use Amasty\ReportBuilder\Api\Data\AxisInterface;
use Amasty\ReportBuilder\Api\Data\ChartInterface;
use Amasty\ReportBuilder\Api\Data\ReportInterface;
use Amasty\ReportBuilder\Model\Chart\Axis\GetAvailableAxisTypes;
use Amasty\ReportBuilder\Model\ResourceModel\Report\Axis as AxisResource;
use Amasty\ReportBuilder\Model\ResourceModel\Report\Chart as ChartResource;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\Declaration\Schema\Db\DDLTriggerInterface;
use Magento\Framework\Setup\Declaration\Schema\ElementHistory;

class MigrateAxis implements DDLTriggerInterface
{
    private const MATCH_PATTERN = 'migrateReportBuilderAxis';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    public function isApplicable(string $statement): bool
    {
        return $statement === self::MATCH_PATTERN;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getCallback(ElementHistory $tableHistory): callable
    {
        return function () {
            $connection = $this->resourceConnection->getConnection();
            $reportTable = $this->resourceConnection->getTableName(ReportInterface::MAIN_TABLE);
            $chartTable = $this->resourceConnection->getTableName(ChartResource::MAIN_TABLE);
            $axisTable = $this->resourceConnection->getTableName(AxisResource::MAIN_TABLE);

            $select = $connection->select()->from(
                $reportTable,
                [ReportInterface::CHART_AXIS_X, ReportInterface::CHART_AXIS_Y]
            )->join(
                $chartTable,
                sprintf(
                    '%s.%s = %s.%s',
                    $reportTable,
                    ReportInterface::REPORT_ID,
                    $chartTable,
                    ChartInterface::REPORT_ID
                ),
                [ChartInterface::ID]
            )->where(ReportInterface::DISPLAY_CHART . ' = 1');

            $dataToInsert = [];
            $rows = $connection->fetchAll($select);
            foreach ($rows as $row) {
                $dataToInsert[] = [
                    AxisInterface::CHART_ID => $row[ChartInterface::ID],
                    AxisInterface::TYPE => GetAvailableAxisTypes::AXIS_X,
                    AxisInterface::VALUE => $row[ReportInterface::CHART_AXIS_X]
                ];
                $dataToInsert[] = [
                    AxisInterface::CHART_ID => $row[ChartInterface::ID],
                    AxisInterface::TYPE => GetAvailableAxisTypes::AXIS_Y,
                    AxisInterface::VALUE => $row[ReportInterface::CHART_AXIS_Y]
                ];
            }
            if ($dataToInsert) {
                $connection->insertMultiple(
                    $axisTable,
                    $dataToInsert
                );
            }
        };
    }
}
