<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Report\Chart;

use Amasty\ReportBuilder\Api\Data\AxisInterface;
use Amasty\ReportBuilder\Api\Data\ChartInterface;
use Amasty\ReportBuilder\Model\Report\Chart\Axis\Command\SaveMultipleInterface as SaveAxisesCommand;
use Amasty\ReportBuilder\Model\Report\Chart\Command\SaveInterface as SaveChartCommand;

class SaveChart
{
    /**
     * @var SaveChartCommand
     */
    private $saveChartCommand;

    /**
     * @var SaveAxisesCommand
     */
    private $saveAxisesCommand;

    public function __construct(SaveChartCommand $saveChartCommand, SaveAxisesCommand $saveAxisesCommand)
    {
        $this->saveChartCommand = $saveChartCommand;
        $this->saveAxisesCommand = $saveAxisesCommand;
    }

    /**
     * @param ChartInterface $chart
     * @param AxisInterface[] $axises
     */
    public function execute(ChartInterface $chart, array $axises): void
    {
        $this->saveChartCommand->execute($chart);
        $this->saveAxisesCommand->execute((int) $chart->getId(), $axises);
    }
}
