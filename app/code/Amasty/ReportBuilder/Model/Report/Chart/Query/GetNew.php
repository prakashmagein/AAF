<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Report\Chart\Query;

use Amasty\ReportBuilder\Api\Data\ChartInterface;
use Amasty\ReportBuilder\Model\Report\ChartFactory;

class GetNew implements GetNewInterface
{
    /**
     * @var ChartFactory
     */
    private $chartFactory;

    public function __construct(ChartFactory $chartFactory)
    {
        $this->chartFactory = $chartFactory;
    }

    public function execute(): ChartInterface
    {
        return $this->chartFactory->create();
    }
}
