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

use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ItemFilter;
use Magento\Framework\Exception\ConfigurationMismatchException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\CreditmemoItemInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\Order\Creditmemo\Item as CreditmemoItem;
use Magento\Sales\Model\Order\Item;
use Aheadworks\RewardPoints\Model\Calculator\Earning\Calculator\CreditMemo as CreditMemoCalculator;

class CreditmemoItemsResolver
{
    /**
     * @param OrderItemsResolver $orderItemsResolver
     * @param CreditMemoCalculator $creditMemoCalculator
     * @param ItemFilter $itemFilter
     */
    public function __construct(
        private readonly OrderItemsResolver $orderItemsResolver,
        private readonly CreditMemoCalculator $creditMemoCalculator,
        private readonly ItemFilter $itemFilter
    ) {
    }

    /**
     * Resolve credit memo items
     *
     * @param CreditmemoInterface $creditmemo
     * @return CreditmemoItem[]
     * @throws ConfigurationMismatchException
     * @throws NoSuchEntityException
     */
    public function getItems(CreditmemoInterface $creditmemo): array
    {
        $creditmemoItems = [];
        $orderItems = $this->orderItemsResolver->getOrderItems($creditmemo->getOrderId());
        if (!empty($orderItems)) {
            /** @var CreditmemoItem[] $items */
            $items = $creditmemo->getItems();
            $qty = $this->creditMemoCalculator->getQtyItems($orderItems, $creditmemo->getData('total_qty'));
            foreach ($items as $item) {
                if ($this->isNeedToProcessCreditmemoItem($item, $orderItems)) {
                    /** @var Item $orderItem */
                    $orderItem = $orderItems[$item->getOrderItemId()];
                    $orderParentItemId = $orderItem->getParentItemId();
                    $parentItemId = null;
                    if ($orderParentItemId) {
                        $parentItem = $this->getCreditmemoItemByOrderItemId((int)$orderParentItemId, $items);
                        $parentItemId = is_object($parentItem) ? $parentItem->getEntityId() : null;
                    }
                    $item
                        ->setItemId($item->getEntityId())
                        ->setParentItemId($parentItemId)
                        ->setProductType($orderItem->getProductType())
                        ->setIsChildrenCalculated($orderItem->isChildrenCalculated())
                        ->setAwRpAmountForOtherDeduction(
                            $this->creditMemoCalculator->calculateAmount($orderItem, $creditmemo, $qty)
                        );

                    $creditmemoItems[$item->getEntityId()] = $item;
                }
            }
        }
        return $this->itemFilter->filterItemsWithoutDiscount($creditmemoItems);
    }

    /**
     * Check if need to process specified creditmemo item
     *
     * @param CreditmemoItem $creditmemoItem
     * @param OrderItemInterface[] $orderItems
     * @return bool
     */
    private function isNeedToProcessCreditmemoItem(CreditmemoItem $creditmemoItem, array $orderItems): bool
    {
        return isset($orderItems[$creditmemoItem->getOrderItemId()])
            && $creditmemoItem->getQty() > 0;
    }

    /**
     * Get creditmemo item by order item
     *
     * @param int $orderItemId
     * @param CreditmemoItemInterface[] $creditmemoItems
     * @return CreditmemoItemInterface|null
     */
    private function getCreditmemoItemByOrderItemId(int $orderItemId, array $creditmemoItems): ?CreditmemoItemInterface
    {
        foreach ($creditmemoItems as $creditmemoItem) {
            if ($creditmemoItem->getOrderItemId() == $orderItemId) {
                return $creditmemoItem;
            }
        }
        return null;
    }
}
