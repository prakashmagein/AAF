<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Ui\DataProvider\Form\Report\Modifier;

use Amasty\ReportBuilder\Api\Data\AxisInterface;
use Amasty\ReportBuilder\Api\Data\ChartInterface;
use Amasty\ReportBuilder\Model\Chart\IsChartTypeAvailable;
use Amasty\ReportBuilder\Model\Report\Chart\Axis\Query\GetAxisListInterface;
use Amasty\ReportBuilder\Model\ReportRegistry;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class AddChartData implements ModifierInterface
{
    public const CHART_SCOPE = 'chart';
    public const AXISES_SCOPE = 'axises';

    /**
     * @var ReportRegistry
     */
    private $reportRegistry;

    /**
     * @var GetAxisListInterface
     */
    private $getAxisList;

    /**
     * @var IsChartTypeAvailable
     */
    private $isChartTypeAvailable;

    public function __construct(
        ReportRegistry $reportRegistry,
        GetAxisListInterface $getAxisList,
        IsChartTypeAvailable $isChartTypeAvailable
    ) {

        $this->reportRegistry = $reportRegistry;
        $this->getAxisList = $getAxisList;
        $this->isChartTypeAvailable = $isChartTypeAvailable;
    }

    public function modifyData(array $data): array
    {
        $report = $this->reportRegistry->getReport();
        $chart = $report->getExtensionAttributes()->getChart();
        if ($chart === null || !$this->isChartTypeAvailable->execute($chart->getChartType())) {
            return $data;
        }

        $axises = $this->getAxisList->execute((int) $chart->getId());

        $chartData = [
            ChartInterface::ID => $chart->getId(),
            ChartInterface::CHART_TYPE => $chart->getChartType()
        ];
        foreach ($axises as $axis) {
            $chartData[self::AXISES_SCOPE][] = [
                AxisInterface::ID => $axis->getId(),
                AxisInterface::TYPE => $axis->getType(),
                AxisInterface::VALUE => $axis->getValue()
            ];
        }
        $data[$report->getReportId()][self::CHART_SCOPE] = $chartData;

        return $data;
    }

    public function modifyMeta(array $meta): array
    {
        return $meta;
    }
}
