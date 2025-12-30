<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://aheadworks.com/end-user-license-agreement/
 *
 * @package    RewardPoints
 * @version    2.4.0
 * @copyright  Copyright (c) 2024 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\RawItemProcessor;

use Magento\Framework\Exception\ConfigurationMismatchException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\InvoiceItemInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use Aheadworks\RewardPoints\Model\Calculator\Earning\Calculator\Invoice as InvoiceCalculator;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ItemFilter;

class InvoiceItemsResolver
{
    /**
     * @param OrderItemsResolver $orderItemsResolver
     * @param InvoiceCalculator $invoiceCalculator
     * @param ItemFilter $itemFilter
     */
    public function __construct(
        private readonly OrderItemsResolver $orderItemsResolver,
        private readonly InvoiceCalculator $invoiceCalculator,
        private readonly ItemFilter $itemFilter
    ) {
    }

    /**
     * Resolve invoice items
     *
     * @param InvoiceInterface $invoice
     * @return InvoiceItem[]
     * @throws ConfigurationMismatchException
     * @throws NoSuchEntityException
     */
    public function getItems(InvoiceInterface $invoice): array
    {
        $invoiceItems = [];
        /** @var OrderItemInterface $orderItems */
        $orderItems = $this->orderItemsResolver->getOrderItems($invoice->getOrderId());
        if (!empty($orderItems)) {
            /** @var InvoiceItem[] $items */
            $items = $invoice->getItems();
            $qty = $this->invoiceCalculator->getQtyItems($orderItems, (int) $invoice->getTotalQty());
            foreach ($items as $item) {
                if (isset($orderItems[$item->getOrderItemId()])) {
                    /** @var OrderItemInterface $orderItem */
                    $orderItem = $orderItems[$item->getOrderItemId()];
                    $orderParentItemId = $orderItem->getParentItemId();
                    $parentItemId = null;
                    if ($orderParentItemId) {
                        $parentItem = $this->getInvoiceItemByOrderItemId((int)$orderParentItemId, $items);
                        $parentItemId = $parentItem->getEntityId();
                    }
                    $item
                        ->setItemId($item->getEntityId())
                        ->setParentItemId($parentItemId)
                        ->setProductType($orderItem->getProductType())
                        ->setIsChildrenCalculated($orderItem->isChildrenCalculated())
                        ->setAwRpAmountForOtherDeduction(
                            $this->invoiceCalculator->calculateAmount($orderItem, $invoice, $qty)
                        );

                    $invoiceItems[$item->getEntityId()] = $item;
                }
            }
        }
        return $this->itemFilter->filterItemsWithoutDiscount($invoiceItems);
    }

    /**
     * Get invoice item by order item
     *
     * @param int $orderItemId
     * @param InvoiceItemInterface[] $invoiceItems
     * @return InvoiceItemInterface|null
     */
    private function getInvoiceItemByOrderItemId(int $orderItemId, array $invoiceItems): ?InvoiceItemInterface
    {
        foreach ($invoiceItems as $invoiceItem) {
            if ($invoiceItem->getOrderItemId() == $orderItemId) {
                return $invoiceItem;
            }
        }
        return null;
    }
}
