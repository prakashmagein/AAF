<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Model\Order;

use Magento\Sales\Model\Order;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\ResourceModel\Order\Collection;

class UpdateOrderIncrementId extends AbstractUpdateOrder
{
    /**
     * @param Order $order
     * @param array $logOfChanges
     * @param Quote|null $quote
     * @param string|null $orderNewShippingMethod
     * @return bool
     */
    public function execute(
        Order $order,
        array &$logOfChanges,
        Quote $quote = null,
        string $orderNewIncrementId = null,
        Collection $collection = null
    ): bool {
        $elementWithIncrementId = $collection
            ->addFieldToFilter('entity_id', ['neq' => $order->getId()])
            ->addFieldToFilter('increment_id', $orderNewIncrementId);

        if (count($elementWithIncrementId)) {
            return false;
        }

        $orderCurrentIncremendId = (string)$order->getIncrementId();
        if ($orderCurrentIncremendId !== $orderNewIncrementId) {
            $this->writeChanges(self::SECTION_ORDER_INFO, $logOfChanges, 'increment_id', 'ID', $orderCurrentIncremendId, $orderNewIncrementId);
            $order->setIncrementId($orderNewIncrementId);
        }

        return true;
    }
}
