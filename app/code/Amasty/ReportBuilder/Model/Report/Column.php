<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Report;

use Amasty\ReportBuilder\Api\Data\ReportColumnExtensionInterface;
use Amasty\ReportBuilder\Api\Data\ReportColumnInterface;
use Amasty\ReportBuilder\Model\ResourceModel\Report\Column as ColumnResourceModel;
use Magento\Framework\Model\AbstractExtensibleModel;

class Column extends AbstractExtensibleModel implements ReportColumnInterface
{
    protected function _construct()
    {
        $this->_init(ColumnResourceModel::class);
    }

    public function getColumnAlias(): string
    {
        return str_replace('.', '_', $this->getColumnId());
    }

    public function getId(): ?int
    {
        return $this->getDataByKey(self::ID) === null ? null
            : (int) $this->getDataByKey(self::ID);
    }

    public function setId($id): void
    {
        $this->setData(self::ID, (int)$id);
    }

    public function getColumnId(): ?string
    {
        return $this->getDataByKey(self::COLUMN_ID);
    }

    public function setColumnId(?string $columnId): void
    {
        $this->setData(self::COLUMN_ID, $columnId);
    }

    public function getReportId(): ?int
    {
        return $this->getDataByKey(self::REPORT_ID) === null ? null
            : (int) $this->getDataByKey(self::REPORT_ID);
    }

    public function setReportId(?int $reportId): void
    {
        $this->setData(self::REPORT_ID, $reportId);
    }

    public function getIsDateFilter(): ?bool
    {
        return $this->getDataByKey(self::IS_DATE_FILTER) === null ? null
            : (bool) $this->getDataByKey(self::IS_DATE_FILTER);
    }

    public function setIsDateFilter(?bool $isDateFilter): void
    {
        $this->setData(self::IS_DATE_FILTER, $isDateFilter);
    }

    public function getOrder(): ?int
    {
        return $this->getDataByKey(self::ORDER) === null ? null
            : (int) $this->getDataByKey(self::ORDER);
    }

    public function setOrder(?int $order): void
    {
        $this->setData(self::ORDER, $order);
    }

    public function getFilter(): ?string
    {
        return $this->getDataByKey(self::FILTER);
    }

    public function setFilter(?string $filter): void
    {
        $this->setData(self::FILTER, $filter);
    }

    public function getVisibility(): ?bool
    {
        return $this->getDataByKey(self::VISIBILITY) === null ? null
            : (bool) $this->getDataByKey(self::VISIBILITY);
    }

    public function setVisibility(?bool $visibility): void
    {
        $this->setData(self::VISIBILITY, $visibility);
    }

    public function getPosition(): ?int
    {
        return $this->getDataByKey(self::POSITION) === null ? null
            : (int) $this->getDataByKey(self::POSITION);
    }

    public function setPosition(?int $position): void
    {
        $this->setData(self::POSITION, $position);
    }

    public function getCustomTitle(): ?string
    {
        return $this->getDataByKey(self::CUSTOM_TITLE);
    }

    public function setCustomTitle(?string $customTitle): void
    {
        $this->setData(self::CUSTOM_TITLE, $customTitle);
    }

    public function getAggregationType(): ?string
    {
        return $this->getDataByKey(self::AGGREGATION_TYPE);
    }

    public function setAggregationType(?string $aggregationType): void
    {
        $this->setData(self::AGGREGATION_TYPE, $aggregationType);
    }

    public function getExtensionAttributes(): ReportColumnExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    public function setExtensionAttributes(ReportColumnExtensionInterface $extensionAttributes): ReportColumnInterface
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
