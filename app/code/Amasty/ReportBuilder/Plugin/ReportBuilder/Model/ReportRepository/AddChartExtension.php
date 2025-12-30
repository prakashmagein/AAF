<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Plugin\ReportBuilder\Model\ReportRepository;

use Amasty\ReportBuilder\Api\Data\ReportInterface;
use Amasty\ReportBuilder\Api\ReportRepositoryInterface;
use Amasty\ReportBuilder\Model\Report\Chart\Query\GetByReportIdInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class AddChartExtension
{
    /**
     * @var GetByReportIdInterface
     */
    private $getByReportId;

    public function __construct(GetByReportIdInterface $getByReportId)
    {
        $this->getByReportId = $getByReportId;
    }

    /**
     * @see ReportRepositoryInterface::getById
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetById(ReportRepositoryInterface $reportRepository, ReportInterface $report): ReportInterface
    {
        $extensionAttributes = $report->getExtensionAttributes();
        if ($extensionAttributes->getChart() === null) {
            try {
                $chart = $this->getByReportId->execute($report->getReportId());
                $extensionAttributes->setChart($chart);
                $report->setExtensionAttributes($extensionAttributes);
            } catch (NoSuchEntityException $e) {
                return $report;
            }
        }

        return $report;
    }
}
