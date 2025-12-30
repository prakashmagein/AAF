<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Report\Chart\Axis\Query;

use Amasty\ReportBuilder\Api\Data\AxisInterface;
use Amasty\ReportBuilder\Model\Report\Chart\AxisFactory;

class GetNew implements GetNewInterface
{
    /**
     * @var AxisFactory
     */
    private $axisFactory;

    public function __construct(AxisFactory $axisFactory)
    {
        $this->axisFactory = $axisFactory;
    }

    public function execute(): AxisInterface
    {
        return $this->axisFactory->create();
    }
}
