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

namespace Aheadworks\RewardPoints\Model\Calculator\Earning\Calculator;

use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\OrderItemInterface;

/**
 * Class CreditMemo
 */
class CreditMemo
{
    /**
     * Get qty without bundle from order items
     *
     * @param OrderItemInterface[] $orderItems
     * @param float $creditMemoQty
     * @return float
     */
    public function getQtyItems(array $orderItems, float $creditMemoQty): float
    {
        $qty = $creditMemoQty;

        foreach ($orderItems as $item) {
            if ($item->getProductType() === 'bundle') {
                $qty -= $item->getQtyRefunded();
            }
        }

        return $qty;
    }

    /**
     * Calculate amount deduction for item
     *
     * @param OrderItemInterface $orderItem
     * @param CreditmemoInterface $creditmemo
     * @param float $qty
     * @return float
     */
    public function calculateAmount(OrderItemInterface $orderItem, CreditmemoInterface $creditmemo, float $qty): float
    {
        $result = 0;
        if ($creditmemo->getBaseRewardCurrencyAmount()) {
            $result = ($creditmemo->getBaseRewardCurrencyAmount() / $qty) * $orderItem->getQtyRefunded();
        }

        return $result;
    }
}
