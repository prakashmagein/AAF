<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\Report\Chart\Axis\Command;

use Amasty\ReportBuilder\Api\Data\AxisInterface;
use Amasty\ReportBuilder\Model\Report\Chart\Axis\Query\GetAxisListInterface;
use Amasty\ReportBuilder\Model\ResourceModel\Report\Axis as AxisResource;
use Amasty\ReportBuilder\Model\ResourceModel\Report\Axis\DeleteAxises;
use Amasty\ReportBuilder\Model\ResourceModel\Report\Axis\InsertMultiple;
use Zend_Db_Exception;

class SaveMultiple implements SaveMultipleInterface
{
    /**
     * @var GetAxisListInterface
     */
    private $getAxisList;

    /**
     * @var AxisResource
     */
    private $axisResource;

    /**
     * @var InsertMultiple
     */
    private $insertMultiple;

    /**
     * @var DeleteAxises
     */
    private $deleteAxises;

    public function __construct(
        GetAxisListInterface $getAxisList,
        AxisResource $axisResource,
        InsertMultiple $insertMultiple,
        DeleteAxises $deleteAxises
    ) {
        $this->getAxisList = $getAxisList;
        $this->axisResource = $axisResource;
        $this->insertMultiple = $insertMultiple;
        $this->deleteAxises = $deleteAxises;
    }

    /**
     * @param int $chartId
     * @param AxisInterface[] $axises
     */
    public function execute(int $chartId, array $axises): void
    {
        $oldAxises = $this->getAxisList->execute($chartId);
        $oldAxisesIds = array_map(function (AxisInterface $axis) {
            return (int) $axis->getId();
        }, $oldAxises);

        $axisesToUpdate = [];
        $axisesToInsert = [];
        $newAxisesIds = [];
        foreach ($axises as $axis) {
            $newAxisesIds[] = (int) $axis->getId();
            if (in_array((int) $axis->getId(), $oldAxisesIds, true)) {
                $axisesToUpdate[] = [
                    AxisInterface::ID => (int) $axis->getId(),
                    AxisInterface::CHART_ID => $chartId,
                    AxisInterface::VALUE => $axis->getValue(),
                    AxisInterface::TYPE => $axis->getType()
                ];
            } else {
                $axisesToInsert[] = [
                    AxisInterface::CHART_ID => $chartId,
                    AxisInterface::VALUE => $axis->getValue(),
                    AxisInterface::TYPE => $axis->getType()
                ];
            }
        }
        $axisesToDelete = array_diff($oldAxisesIds, $newAxisesIds);

        $this->updateAxises($axisesToInsert, $axisesToUpdate, $axisesToDelete);
    }

    /**
     * @throws Zend_Db_Exception
     */
    private function updateAxises(array $axisesToInsert, array $axisesToUpdate, array $axisesToDelete): void
    {
        $this->axisResource->getConnection()->beginTransaction();
        try {
            if ($axisesToInsert) {
                $this->insertMultiple->execute($axisesToInsert);
            }
            if ($axisesToUpdate) {
                $this->insertMultiple->execute($axisesToUpdate);
            }
            if ($axisesToDelete) {
                $this->deleteAxises->execute($axisesToDelete);
            }
            $this->axisResource->getConnection()->commit();
        } catch (Zend_Db_Exception $e) {
            $this->axisResource->getConnection()->rollBack();
            throw new Zend_Db_Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
}
