<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Report\Command;

use Amasty\ReportBuilder\Api\Data\ReportColumnExtensionInterface;
use Amasty\ReportBuilder\Api\Data\ReportColumnInterface;
use Amasty\ReportBuilder\Api\Data\ReportInterface;
use Amasty\ReportBuilder\Api\ReportRepositoryInterface;
use Amasty\ReportBuilder\Model\Report\Chart\Axis\Query\GetAxisListInterface;
use Amasty\ReportBuilder\Model\Report\Chart\Query\GetByReportIdInterface as GetChartByReportId;
use Amasty\ReportBuilder\Model\Report\Chart\SaveChart;
use Magento\Framework\Exception\NoSuchEntityException;

class Duplicate implements DuplicateInterface
{
    /**
     * @var ReportRepositoryInterface
     */
    private $reportRepository;

    /**
     * @var GetChartByReportId
     */
    private $getChartByReportId;

    /**
     * @var GetAxisListInterface
     */
    private $getAxisList;

    /**
     * @var SaveChart
     */
    private $saveChart;

    public function __construct(
        ReportRepositoryInterface $reportRepository,
        GetChartByReportId $getChartByReportId,
        GetAxisListInterface $getAxisList,
        SaveChart $saveChart
    ) {
        $this->reportRepository = $reportRepository;
        $this->getChartByReportId = $getChartByReportId;
        $this->getAxisList = $getAxisList;
        $this->saveChart = $saveChart;
    }

    public function execute(ReportInterface $report): ReportInterface
    {
        $newReport = clone $report;
        $this->processReportColumnsClone($newReport);
        $newReport->setReportId(null);
        $newReport->setName(__('Copy of %1', $report->getName())->render());
        $this->reportRepository->save($newReport);
        $this->processCharts($report->getReportId(), $newReport->getReportId());

        return $newReport;
    }

    /**
     * Prepare report columns to duplication.
     */
    private function processReportColumnsClone(ReportInterface $report): void
    {
        $columns = $report->getAllColumns();

        foreach ($columns as $index => $columnData) {
            unset($columns[$index][ReportColumnInterface::REPORT_ID], $columns[$index][ReportColumnInterface::ID]);

            if (isset($columnData[ReportColumnInterface::EXTENSION_ATTRIBUTES_KEY])) {
                /** @var ReportColumnExtensionInterface $extensionAttributes */
                $extensionAttributes = clone $columnData[ReportColumnInterface::EXTENSION_ATTRIBUTES_KEY];
                $columns[$index][ReportColumnInterface::EXTENSION_ATTRIBUTES_KEY] = $extensionAttributes;
            }
        }

        $report->setColumns($columns);
    }

    /**
     * Create and save duplicate for chart & axises.
     */
    private function processCharts(int $oldReportId, int $newReportId): void
    {
        try {
            $chart = $this->getChartByReportId->execute($oldReportId);
        } catch (NoSuchEntityException $e) {
            return;
        }

        $newChart = clone $chart;
        $newChart->setId(null);
        $newChart->setReportId($newReportId);

        $axises = $this->getAxisList->execute((int) $chart->getId());
        $newAxises = [];
        foreach ($axises as $axis) {
            $newAxis = clone $axis;
            $newAxis->setChartId(null);
            $newAxises[] = $newAxis;
        }

        $this->saveChart->execute($newChart, $newAxises);
    }
}
