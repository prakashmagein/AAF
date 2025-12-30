<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\ResourceModel\Report\Axis;

use Amasty\ReportBuilder\Model\Report\Chart\Axis as AxisModel;
use Amasty\ReportBuilder\Model\ResourceModel\Report\Axis as AxisResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(AxisModel::class, AxisResource::class);
    }
}
