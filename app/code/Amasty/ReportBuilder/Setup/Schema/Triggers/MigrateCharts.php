<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Setup\Schema\Triggers;

use Amasty\ReportBuilder\Api\Data\ChartInterface;
use Amasty\ReportBuilder\Api\Data\ReportInterface;
use Amasty\ReportBuilder\Model\Chart\GetAvailableChartTypes;
use Amasty\ReportBuilder\Model\ResourceModel\Report\Chart as ChartResource;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\Declaration\Schema\Db\DDLTriggerInterface;
use Magento\Framework\Setup\Declaration\Schema\ElementHistory;
use Zend_Db_Expr;

class MigrateCharts implements DDLTriggerInterface
{
    private const MATCH_PATTERN = 'migrateReportBuilderCharts';

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
        $new = $tableHistory->getNew();
        $old = $tableHistory->getOld();
        return function () use ($new, $old) {
            $connection = $this->resourceConnection->getConnection();

            $select = $connection->select()->from(
                $this->resourceConnection->getTableName(ReportInterface::MAIN_TABLE),
                [
                    ChartInterface::REPORT_ID => ReportInterface::REPORT_ID,
                    ChartInterface::CHART_TYPE => new Zend_Db_Expr(
                        sprintf('\'%s\'', GetAvailableChartTypes::LINEAR)
                    )
                ]
            )->where(ReportInterface::DISPLAY_CHART . ' = 1');

            $insertQuery = $connection->insertFromSelect(
                $select,
                $this->resourceConnection->getTableName(ChartResource::MAIN_TABLE),
                [
                    ChartInterface::REPORT_ID, ChartInterface::CHART_TYPE
                ]
            );
            $connection->query($insertQuery);
        };
    }
}
