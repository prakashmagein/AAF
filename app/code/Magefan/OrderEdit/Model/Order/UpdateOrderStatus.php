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

class UpdateOrderStatus extends AbstractUpdateOrder
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
        string $orderNewStatus = null,
        Collection $collection = null
    ): bool {
        $statusToState = [];
        $connection = $collection->getConnection();
        $stateTableName = $collection->getTable('sales_order_status_state');
        $select = $connection->select()->from(['cps' => $stateTableName]);

        foreach ($connection->fetchAll($select) as $item) {
            $statusToState[$item['status']] = $item['state'];
        }

        $orderCurrentStatus = (string)$order->getStatus();

        if ($orderCurrentStatus !== $orderNewStatus) {
            $this->writeChanges(self::SECTION_ORDER_INFO, $logOfChanges, 'status', 'Status', $orderCurrentStatus, $orderNewStatus);
            $order->setStatus($orderNewStatus);

            $order->setData('mf_grid_inline_edit', 1);

            if (isset($statusToState[$orderNewStatus])) {
                $order->setData('pre_state', $order->getState());
                $order->setState($statusToState[$orderNewStatus]);
            }
        }

        return true;
    }
}
