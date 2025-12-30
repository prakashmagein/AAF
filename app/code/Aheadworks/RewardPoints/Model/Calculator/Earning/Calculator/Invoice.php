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

use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderItemInterface;

/**
 * Class Invoice
 */
class Invoice
{
    /**
     * Get qty without bundle from order items
     *
     * @param OrderItemInterface[] $orderItems
     * @param float $invoiceQty
     * @return float
     */
    public function getQtyItems(array $orderItems, float $invoiceQty): float
    {
        $qty = $invoiceQty;

        foreach ($orderItems as $item) {
            if ($item->getProductType() === 'bundle') {
                $qty -= $item->getQtyOrdered();
            }
        }

        return $qty;
    }

    /**
     * Calculate amount deduction for item
     *
     * @param OrderItemInterface $orderItem
     * @param InvoiceInterface $invoice
     * @param float $qty
     * @return float
     */
    public function calculateAmount(OrderItemInterface $orderItem, InvoiceInterface $invoice, float $qty): float
    {
        $result = 0;
        if ($invoice->getBaseRewardCurrencyAmount()) {
            $result = ($invoice->getBaseRewardCurrencyAmount() / $qty) * $orderItem->getQtyOrdered();
        }

        return $result;
    }
}
