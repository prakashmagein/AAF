<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Report\Chart\Axis\Query;

use Amasty\ReportBuilder\Api\Data\AxisInterface;
use Amasty\ReportBuilder\Model\Report\Chart\Axis;
use Amasty\ReportBuilder\Model\Report\Chart\AxisFactory;
use Amasty\ReportBuilder\Model\ResourceModel\Report\Axis as AxisResource;
use Magento\Framework\Exception\NoSuchEntityException;

class GetById implements GetByIdInterface
{
    /**
     * @var AxisFactory
     */
    private $axisFactory;

    /**
     * @var AxisResource
     */
    private $axisResource;

    public function __construct(AxisFactory $axisFactory, AxisResource $axisResource)
    {
        $this->axisFactory = $axisFactory;
        $this->axisResource = $axisResource;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function execute(int $id): AxisInterface
    {
        /** @var AxisInterface|Axis $axis */
        $axis = $this->axisFactory->create();
        $this->axisResource->load($axis, $id);

        if ($axis->getId() === null) {
            throw new NoSuchEntityException(
                __('Axis with id "%value" does not exist.', ['value' => $id])
            );
        }

        return $axis;
    }
}
