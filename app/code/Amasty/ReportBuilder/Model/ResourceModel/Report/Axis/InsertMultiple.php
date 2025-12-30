<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\ResourceModel\Report\Axis;

use Amasty\ReportBuilder\Model\ResourceModel\Report\Axis as AxisResource;

class InsertMultiple
{
    /**
     * @var AxisResource
     */
    private $axisResource;

    public function __construct(AxisResource $axisResource)
    {
        $this->axisResource = $axisResource;
    }

    public function execute(array $data): void
    {
        $this->axisResource->getConnection()->insertOnDuplicate(
            $this->axisResource->getTable(AxisResource::MAIN_TABLE),
            $data
        );
    }
}
