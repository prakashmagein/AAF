<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Report\Chart\Axis\Command;

use Amasty\ReportBuilder\Api\Data\AxisInterface;
use Zend_Db_Exception;

interface SaveMultipleInterface
{
    /**
     * @param int $chartId
     * @param AxisInterface[] $axises
     * @throws Zend_Db_Exception
     */
    public function execute(int $chartId, array $axises): void;
}
