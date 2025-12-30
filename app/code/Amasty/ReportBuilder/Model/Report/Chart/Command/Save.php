<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Report\Chart\Command;

use Amasty\ReportBuilder\Api\Data\ChartInterface;
use Amasty\ReportBuilder\Model\Report\Chart;
use Amasty\ReportBuilder\Model\ResourceModel\Report\Chart as ChartResource;
use Exception;
use Magento\Framework\Exception\CouldNotSaveException;
use Psr\Log\LoggerInterface;

class Save implements SaveInterface
{
    /**
     * @var ChartResource
     */
    private $chartResource;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ChartResource $chartResource,
        LoggerInterface $logger
    ) {
        $this->chartResource = $chartResource;
        $this->logger = $logger;
    }

    /**
     * @param ChartInterface|Chart $chart
     * @throws CouldNotSaveException
     */
    public function execute(ChartInterface $chart): void
    {
        try {
            $this->chartResource->save($chart);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CouldNotSaveException(__('Could not save Chart'), $e);
        }
    }
}
