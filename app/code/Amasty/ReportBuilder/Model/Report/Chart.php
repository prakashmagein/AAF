<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Report;

use Amasty\ReportBuilder\Api\Data\ChartInterface;
use Amasty\ReportBuilder\Model\ResourceModel\Report\Chart as ChartResource;
use Magento\Framework\Model\AbstractModel;

class Chart extends AbstractModel implements ChartInterface
{
    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ChartResource::class);
    }

    public function getReportId(): ?int
    {
        return $this->hasData(ChartInterface::REPORT_ID)
            ? (int) $this->_getData(ChartInterface::REPORT_ID)
            : null;
    }

    public function setReportId(int $reportId): void
    {
        $this->setData(ChartInterface::REPORT_ID, $reportId);
    }

    public function getChartType(): ?string
    {
        return $this->hasData(ChartInterface::CHART_TYPE)
            ? (string) $this->_getData(ChartInterface::CHART_TYPE)
            : null;
    }

    public function setChartType(string $chartType): void
    {
        $this->setData(ChartInterface::CHART_TYPE, $chartType);
    }
}
