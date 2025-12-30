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
namespace Aheadworks\RewardPoints\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class Aheadworks\RewardPoints\Observer\IncreaseOrderRewardPointsInvoicedAmount
 */
class IncreaseOrderRewardPointsInvoicedAmount implements ObserverInterface
{
    /**
     * Increase order aw_reward_points_invoiced attribute based on created invoice
     * used for event: sales_order_invoice_register
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();
        if ($invoice->getBaseAwRewardPointsAmount()) {
            $order->setBaseAwRewardPointsInvoiced(
                $order->getBaseAwRewardPointsInvoiced() + $invoice->getBaseAwRewardPointsAmount()
            );
            $order->setAwRewardPointsInvoiced(
                $order->getAwRewardPointsInvoiced() + $invoice->getAwRewardPointsAmount()
            );
        }
        return $this;
    }
}
