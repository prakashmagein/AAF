<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Report;

use Amasty\ReportBuilder\Api\Data\ReportColumnInterface;
use Amasty\ReportBuilder\Api\Data\ReportColumnInterfaceFactory;
use Amasty\ReportBuilder\Model\ResourceModel\Report\Column as ResourceModel;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\ResourceConnection;

class ColumnSave
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var ResourceModel
     */
    private $resourceModel;

    /**
     * @var ColumnProvider
     */
    private $columnProvider;

    /**
     * @var ReportColumnInterfaceFactory
     */
    private $modelFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    public function __construct(
        ResourceConnection $resourceConnection,
        ResourceModel $resourceModel,
        ColumnProvider $columnProvider,
        ReportColumnInterfaceFactory $modelFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->resourceModel = $resourceModel;
        $this->columnProvider = $columnProvider;
        $this->modelFactory = $modelFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * @param int $reportId
     * @param array $columnsData
     *
     * @return Column[]
     * @api
     */
    public function saveReportColumns(int $reportId, array $columnsData): array
    {
        $savedColumns = [];

        $oldColumn = $this->columnProvider->getColumnsByReportId($reportId);

        foreach ($columnsData as $columnData) {
            $columnId = $columnData[ReportColumnInterface::COLUMN_ID];
            $column = $oldColumn[$columnId] ?? $this->modelFactory->create();

            $this->dataObjectHelper->populateWithArray($column, $columnData, ReportColumnInterface::class);
            $column->setReportId($reportId);

            $this->resourceModel->save($column);
            $savedColumns[$columnId] = $column;
        }

        $this->deleteColumns($reportId, array_keys($savedColumns));

        $this->columnProvider->clear($reportId);

        return $savedColumns;
    }

    /**
     * @param int $reportId
     * @param string[] $savedColumnIds
     */
    private function deleteColumns(int $reportId, array $savedColumnIds): void
    {
        $connection = $this->resourceConnection->getConnection();
        $mainTable = $this->resourceModel->getMainTable();
        $selectToDelete = $connection->select()->from(
            $mainTable,
            [ReportColumnInterface::COLUMN_ID]
        );
        $selectToDelete->where(ReportColumnInterface::REPORT_ID . ' = ?', $reportId);
        if (!empty($savedColumnIds)) {
            $selectToDelete->where(ReportColumnInterface::COLUMN_ID . ' NOT IN (?)', $savedColumnIds);
        }

        $connection->query($selectToDelete->deleteFromSelect($mainTable));
    }
}
