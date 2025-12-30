<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Report\Chart;

use Amasty\ReportBuilder\Api\Data\AxisInterface;
use Amasty\ReportBuilder\Model\ResourceModel\Report\Axis as AxisResource;
use Magento\Framework\Model\AbstractModel;

class Axis extends AbstractModel implements AxisInterface
{
    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(AxisResource::class);
    }

    public function getChartId(): ?int
    {
        return $this->hasData(AxisInterface::CHART_ID)
            ? (int) $this->_getData(AxisInterface::CHART_ID)
            : null;
    }

    public function setChartId(?int $chartId): void
    {
        $this->setData(AxisInterface::CHART_ID, $chartId);
    }

    public function getType(): ?string
    {
        return $this->hasData(AxisInterface::TYPE)
            ? (string) $this->_getData(AxisInterface::TYPE)
            : null;
    }

    public function setType(string $type): void
    {
        $this->setData(AxisInterface::TYPE, $type);
    }

    public function getValue(): ?string
    {
        return $this->hasData(AxisInterface::VALUE)
            ? (string) $this->_getData(AxisInterface::VALUE)
            : null;
    }

    public function setValue(string $value): void
    {
        $this->setData(AxisInterface::VALUE, $value);
    }
}
