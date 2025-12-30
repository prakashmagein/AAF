<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Backend\Chart\Axis;

use Amasty\ReportBuilder\Api\Data\AxisInterface;
use Amasty\ReportBuilder\Model\Report\Chart\Axis\Query\GetByIdInterface;
use Amasty\ReportBuilder\Model\Report\Chart\Axis\Query\GetNewInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class Initialization
{
    /**
     * @var GetByIdInterface
     */
    private $getById;

    /**
     * @var GetNewInterface
     */
    private $getNew;

    public function __construct(GetByIdInterface $getById, GetNewInterface $getNew)
    {
        $this->getById = $getById;
        $this->getNew = $getNew;
    }

    /**
     * @return AxisInterface[]
     * @throws LocalizedException
     * @throws InputException
     */
    public function execute(array $data): array
    {
        $axises = [];
        foreach ($data as $axisData) {
            if (!empty($axisData[AxisInterface::ID])) {
                try {
                    $axis = $this->getById->execute((int) $axisData[AxisInterface::ID]);
                } catch (NoSuchEntityException $e) {
                    throw new LocalizedException(__($e->getRawMessage(), $e->getParameters()));
                }
            } else {
                $axis = $this->getNew->execute();
            }

            if (!empty($axisData[AxisInterface::TYPE])) {
                $axis->setType($axisData[AxisInterface::TYPE]);
            } else {
                throw InputException::requiredField(__('Axis Name'));
            }
            if (!empty($axisData[AxisInterface::VALUE])) {
                $axis->setValue($axisData[AxisInterface::VALUE]);
            } else {
                throw InputException::requiredField(__('Axis Value'));
            }

            $axises[] = $axis;
        }

        return $axises;
    }
}
