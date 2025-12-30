<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\ResourceModel\Report;

use Amasty\ReportBuilder\Api\Data\AxisInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Axis extends AbstractDb
{
    public const MAIN_TABLE = 'amasty_report_builder_report_chart_axis';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE, AxisInterface::ID);
    }
}
