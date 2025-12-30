<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Model\Invoice;

use Magento\Sales\Model\Order;
use Magefan\OrderEdit\Model\Order\UpdateOrderItems;
use Magento\Sales\Model\Order\ItemRepository as OrderItemRepository;

class NormalizeOrderItems
{
    protected $lastInvoice;

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

        $this->normalizeDiscount($order);
    }

    /**
     * @param Order $order
     * @return void
     */
    protected function normalizeDiscount(Order $order): void
    {
        $discountInvoicedOfFakeDeletedOrderItems = $this->getDiscountInvoicedOfFakeDeleteOrderItems($order);

        foreach ($order->getAllItems() as $orderItem) {
            if (UpdateOrderItems::SKIP_PARENT_ITEM_ID != $orderItem->getParentItemId()) {
                if ($parentItem = $orderItem->getParentItem()) {
                    if (UpdateOrderItems::SKIP_PARENT_ITEM_ID  == $parentItem->getParentItemId()) {
                        continue;
                    }

                    $orderItem = $parentItem;
                }

                $orderItem->setDiscountInvoiced($discountInvoicedOfFakeDeletedOrderItems['discountInvoiced'] ?? 0.0);
                $orderItem->setBaseDiscountInvoiced($discountInvoicedOfFakeDeletedOrderItems['baseDiscountInvoiced'] ?? 0.0);

                try {
                    $this->orderItemRepository->save($orderItem);
                } catch (\Exception $e) {
                    break;
                }

                break;
            }
        }
    }

    /**
     * @param Order $order
     * @return float[]
     */
    protected function getDiscountInvoicedOfFakeDeleteOrderItems(Order $order): array
    {
        $discountInvoicedOfFakeDeletedOrderItems = ['discountInvoiced' => 0.0, 'baseDiscountInvoiced' => 0.0];

        $lastInvoice = $this->getLastInvoice($order);
        if ($lastInvoice) {
            foreach ($lastInvoice->getItems() as $invoiceItem) {
                $orderItem = $order->getItemById($invoiceItem->getOrderItemId());

                if ($orderItem && (UpdateOrderItems::SKIP_PARENT_ITEM_ID == $orderItem->getParentItemId())) {
                    $discountInvoicedOfFakeDeletedOrderItems['discountInvoiced'] += (float)$orderItem->getDiscountInvoiced();
                    $discountInvoicedOfFakeDeletedOrderItems['baseDiscountInvoiced'] += (float)$orderItem->getBaseDiscountInvoiced();
                }
            }
        }

        return $discountInvoicedOfFakeDeletedOrderItems;
    }

    /**
     * @param Order $order
     * @return false|\Magento\Framework\DataObject
     */
    protected function getLastInvoice(Order $order)
    {
        if (!isset($this->lastInvoice)) {
            $this->lastInvoice = false;

            $invoiceCollection = $order->getInvoiceCollection();

            if ($invoiceCollection->getSize()) {
                $this->lastInvoice =  $invoiceCollection->getLastItem();
            }
        }

        return $this->lastInvoice;
    }
}
