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
use Amasty\ReportBuilder\Model\Report\Chart\Command\SaveInterface as SaveChartCommand;

class SaveChart
{
    /**
     * @var SaveChartCommand
     */
    private $saveChartCommand;

    public function __construct(SaveChartCommand $saveChartCommand)
    {
        $this->saveChartCommand = $saveChartCommand;
    }

    /**
     * @see ReportRepositoryInterface::save
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(ReportRepositoryInterface $reportRepository, ReportInterface $report): ReportInterface
    {
        $extensionAttributes = $report->getExtensionAttributes();
        if ($chart = $extensionAttributes->getChart()) {
            $this->saveChartCommand->execute($chart);
        }

        return $report;
    }
}
