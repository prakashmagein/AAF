<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Report\Chart\Query;

use Amasty\ReportBuilder\Api\Data\ChartInterface;
use Amasty\ReportBuilder\Model\Report\Chart;

interface GetNewInterface
{
    /**
     * @return ChartInterface|Chart
     */
    public function execute(): ChartInterface;
}
