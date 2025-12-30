<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Model\Shipment;

use Magento\Sales\Model\Order;
use Magefan\OrderEdit\Model\Order\UpdateOrderItems;
use Magento\Sales\Model\Order\ItemRepository as OrderItemRepository;

class NormalizeOrderItems
{
    /**
     * @var OrderItemRepository
     */
    protected $orderItemRepository;

    /**
     * @param OrderItemRepository $orderItemRepository
     */
    public function __construct(
        OrderItemRepository  $orderItemRepository
    ) {
        $this->orderItemRepository = $orderItemRepository;
    }

    /**
     * @param Order $order
     * @return void
     */
    public function execute(Order $order): void
    {
        if (!count($order->getInvoiceCollection())) {
            return;
        }

        $this->normalizeQtyShipped($order);
    }

    /**
     * @param Order $order
     * @return void
     */
    protected function normalizeQtyShipped(Order $order): void
    {

        foreach ($order->getAllItems() as $orderItem) {

            if (UpdateOrderItems::SKIP_PARENT_ITEM_ID == $orderItem->getParentItemId()) {
                $orderItem->setQtyShipped((float)$orderItem->getQtyOrdered());

                try {
                    $this->orderItemRepository->save($orderItem);
                } catch (\Exception $e) {

                }
            }
        }
    }
}
