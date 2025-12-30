<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\ResourceModel\Report\Axis;

use Amasty\ReportBuilder\Api\Data\AxisInterface;
use Amasty\ReportBuilder\Model\ResourceModel\Report\Axis as AxisResource;

class DeleteAxises
{
    /**
     * @var AxisResource
     */
    private $axisResource;

    public function __construct(AxisResource $axisResource)
    {
        $this->axisResource = $axisResource;
    }

    /**
     * @param int[] $axisesIds
     */
    public function execute(array $axisesIds): void
    {
        $this->axisResource->getConnection()->delete(
            $this->axisResource->getTable(AxisResource::MAIN_TABLE),
            [sprintf('%s IN (?)', AxisInterface::ID) => $axisesIds]
        );
    }
}
