<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\ColumnOrder;

use Amasty\ReportBuilder\Model\ResourceModel\Report\Data\Select;

class OrderApplier implements OrderApplierInterface
{
    /**
     * @var OrderStorageInterface
     */
    private $orderStorage;

    public function __construct(
        OrderStorageInterface $orderStorage
    ) {
        $this->orderStorage = $orderStorage;
    }

    public function apply(Select $select): void
    {
        foreach ($this->orderStorage->getAllOrders() as $column => $direction) {
            $select->order(sprintf('%s %s', $column, $direction));
        }
    }
}
