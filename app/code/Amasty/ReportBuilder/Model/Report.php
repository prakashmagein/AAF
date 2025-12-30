<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model;

use Amasty\ReportBuilder\Api\Data\ReportExtensionInterface;
use Amasty\ReportBuilder\Api\Data\ReportInterface;
use Amasty\ReportBuilder\Model\ResourceModel\Report as ReportResource;
use Magento\Framework\Api\ExtensionAttributesInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class Report extends AbstractExtensibleModel implements ReportInterface
{
    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ReportResource::class);
    }

    public function getReportId(): int
    {
        return (int) $this->getData(ReportInterface::REPORT_ID);
    }

    public function setReportId(?int $reportId): void
    {
        $this->setData(ReportInterface::REPORT_ID, $reportId);
    }

    public function getName(): ?string
    {
        return $this->getData(ReportInterface::NAME);
    }

    public function setName(string $name): void
    {
        $this->setData(ReportInterface::NAME, $name);
    }

    public function getMainEntity(): ?string
    {
        return $this->getData(ReportInterface::MAIN_ENTITY);
    }

    public function setMainEntity(string $entity): void
    {
        $this->setData(ReportInterface::MAIN_ENTITY, $entity);
    }

    public function getUsePeriod(): bool
    {
        return (bool)$this->getData(ReportInterface::USE_PERIOD);
    }

    public function setUsePeriod(bool $usePeriod = false): void
    {
        $this->setData(ReportInterface::USE_PERIOD, $usePeriod);
    }

    public function getDisplayChart(): bool
    {
        return (bool)$this->getData(ReportInterface::DISPLAY_CHART);
    }

    public function setDisplayChart(bool $displayChart = false): void
    {
        $this->setData(ReportInterface::DISPLAY_CHART, $displayChart);
    }

    public function setColumns(array $columns): void
    {
        $this->setData(ReportInterface::COLUMNS, $columns);
    }

    public function getAllColumns(): array
    {
        return $this->getData(ReportInterface::COLUMNS) ?? [];
    }

    public function setRelationScheme(array $scheme): void
    {
        $this->setData(ReportInterface::SCHEME, $scheme);
    }

    public function getRelationScheme(): array
    {
        return $this->getData(ReportInterface::SCHEME) ?? [];
    }

    public function getAllEntities(): array
    {
        $relations = $this->getRelationScheme();
        $entities = [];

        foreach ($relations as $relation) {
            if (!in_array($relation[ReportInterface::SCHEME_SOURCE_ENTITY], $entities)) {
                $entities[] = $relation[ReportInterface::SCHEME_SOURCE_ENTITY];
            }
            if (!in_array($relation[ReportInterface::SCHEME_ENTITY], $entities)) {
                $entities[] = $relation[ReportInterface::SCHEME_ENTITY];
            }
        }

        return $entities;
    }

    public function getExtensionAttributes(): ReportExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    public function setExtensionAttributes(ReportExtensionInterface $extensionAttributes): void
    {
        $this->_setExtensionAttributes($extensionAttributes);
    }
}
