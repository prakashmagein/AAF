<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Report\Chart\Axis\Query;

use Amasty\ReportBuilder\Api\Data\AxisInterface;

interface GetAxisListInterface
{
    /**
     * Provide array of axis for chart.
     *
     * @return AxisInterface[]
     */
    public function execute(int $chartId): array;
}
