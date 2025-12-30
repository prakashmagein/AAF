<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Report\Chart\Query;

use Amasty\ReportBuilder\Api\Data\ChartInterface;
use Amasty\ReportBuilder\Model\Report\Chart;
use Amasty\ReportBuilder\Model\Report\ChartFactory;
use Amasty\ReportBuilder\Model\ResourceModel\Report\Chart as ChartResource;
use Magento\Framework\Exception\NoSuchEntityException;

class GetByReportId implements GetByReportIdInterface
{
    /**
     * @var ChartFactory
     */
    private $chartFactory;

    /**
     * @var ChartResource
     */
    private $chartResource;

    public function __construct(ChartFactory $chartFactory, ChartResource $chartResource)
    {
        $this->chartFactory = $chartFactory;
        $this->chartResource = $chartResource;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function execute(int $reportId): ChartInterface
    {
        /** @var ChartInterface|Chart $chart */
        $chart = $this->chartFactory->create();
        $this->chartResource->load($chart, $reportId, ChartInterface::REPORT_ID);

        if ($chart->getId() === null) {
            throw new NoSuchEntityException(
                __('Chart for report with id "%value" does not exist.', ['value' => $reportId])
            );
        }

        return $chart;
    }
}
