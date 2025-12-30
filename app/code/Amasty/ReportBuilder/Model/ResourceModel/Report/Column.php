<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\ResourceModel\Report;

use Amasty\ReportBuilder\Api\ColumnInterface;
use Amasty\ReportBuilder\Api\Data\ReportColumnInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Column extends AbstractDb
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'amasty_report_builder_column_resource_model';

    protected function _construct()
    {
        $this->_init(ColumnInterface::COLUMN_TABLE, ReportColumnInterface::ID);
    }
}
