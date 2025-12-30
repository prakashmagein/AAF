<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\ResourceModel;

use Amasty\ReportBuilder\Api\Data\ReportInterface;
use Amasty\ReportBuilder\Api\RelationInterface;
use Amasty\ReportBuilder\Model\Report\Column;
use Amasty\ReportBuilder\Model\Report\ColumnProvider;
use Amasty\ReportBuilder\Model\Report\ColumnSave;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Report extends AbstractDb
{
    /**
     * @var ColumnSave
     */
    private $columnSave;

    /**
     * @var ColumnProvider
     */
    private $columnProvider;

    public function __construct(
        Context $context,
        ColumnSave $columnSave,
        ColumnProvider $columnProvider,
        $connectionName = null
    ) {
        $this->columnSave = $columnSave;
        $this->columnProvider = $columnProvider;
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init(ReportInterface::MAIN_TABLE, ReportInterface::REPORT_ID);
    }

    /**
     * @param ReportInterface $object
     *
     * @return Report
     */
    protected function _afterLoad(AbstractModel $object)
    {
        $this->loadColumns($object);
        $this->loadRelationsScheme($object);

        return parent::_afterLoad($object);
    }

    private function loadColumns(ReportInterface $object): void
    {
        $preparedColumns = [];
        /** @var Column $reportColumn */
        foreach ($this->columnProvider->getColumnsByReportId($object->getReportId()) as $reportColumn) {
            $preparedColumns[$reportColumn->getColumnId()] = $reportColumn->toArray();
        }

        $object->setColumns($preparedColumns);
    }

    private function loadRelationsScheme(ReportInterface $object): void
    {
        $select = $this->getSelectByReportId(RelationInterface::SCHEME_ROUTING_TABLE, $object->getReportId())
            ->order('scheme_id ASC');
        $object->setRelationScheme($this->getConnection()->fetchAll($select));
    }

    private function getSelectByReportId(
        string $tableName,
        int $reportId
    ): Select {
        return $this->getConnection()->select()
            ->from($this->getTable($tableName))
            ->where('report_id = ?', $reportId);
    }

    /**
     * @param ReportInterface $object
     *
     * @return Report
     */
    public function save(AbstractModel $object)
    {
        parent::save($object);

        $this->columnSave->saveReportColumns($object->getReportId(), $object->getAllColumns());
        $this->saveRelations($object);

        return $this;
    }

    private function saveRelations(AbstractModel $report): void
    {
        $this->removeRelations($report);
        $relations = $this->prepareData($report, $report->getRelationScheme());
        if ($relations) {
            $this->getConnection()->insertOnDuplicate(
                $this->getTable(RelationInterface::SCHEME_ROUTING_TABLE),
                $relations
            );
        }
    }

    private function removeRelations(AbstractModel $report): void
    {
        $this->getConnection()->delete(
            $this->getTable(RelationInterface::SCHEME_ROUTING_TABLE),
            sprintf('%s = %s', ReportInterface::REPORT_ID, $report->getReportId())
        );
    }

    private function prepareData(AbstractModel $report, array $data = []): array
    {
        if ($data) {
            foreach ($data as &$item) {
                $item[ReportInterface::REPORT_ID] = $report->getReportId();
            }
        }

        return $data;
    }
}
