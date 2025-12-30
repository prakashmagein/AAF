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
namespace Aheadworks\RewardPoints\Plugin\Block\Tax\Item;

use Magento\Sales\Model\Order\Creditmemo\Item as CreditMemoItem;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use Magento\Sales\Model\Order\Item as OrderItem;

/**
 * Class PriceRenderer
 *
 * @package Aheadworks\RewardPoints\Plugin\Block\Tax\Item
 */
class PriceRenderer
{
    /**
     * Subtract reward points data
     *
     * @param \Magento\Tax\Block\Item\Price\Renderer $subject
     * @param \Closure $proceed
     * @param OrderItem|InvoiceItem|CreditMemoItem $item
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetTotalAmount(
        \Magento\Tax\Block\Item\Price\Renderer $subject,
        \Closure $proceed,
        $item
    ) {
        $totalAmount = $proceed($item);
        // Convert to the same type
        return (string)(float)$totalAmount - (string)(float)$item->getAwRewardPointsAmount();
    }

    /**
     * Subtract reward points data
     *
     * @param \Magento\Tax\Block\Item\Price\Renderer $subject
     * @param \Closure $proceed
     * @param OrderItem|InvoiceItem|CreditMemoItem $item
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetBaseTotalAmount(
        \Magento\Tax\Block\Item\Price\Renderer $subject,
        \Closure $proceed,
        $item
    ) {
        $totalAmount = $proceed($item);
        // Convert to the same type
        return (string)(float)$totalAmount - (string)(float)$item->getBaseAwRewardPointsAmount();
    }
}
