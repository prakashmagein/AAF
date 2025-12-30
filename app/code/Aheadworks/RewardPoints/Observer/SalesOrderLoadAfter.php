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
 * Class SalesOrderLoadAfter
 *
 * @package Aheadworks\RewardPoints\Observer
 */
class SalesOrderLoadAfter implements ObserverInterface
{
    /**
     * Set forced canCreditmemo flag
     * used for event: sales_order_load_after
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        if ($order->canUnhold()) {
            return $this;
        }

        if ($order->isCanceled() || $order->getState() === \Magento\Sales\Model\Order::STATE_CLOSED) {
            return $this;
        }

        if ((abs((float)$order->getAwRewardPointsInvoiced()) - abs((float)$order->getAwRewardPointsRefunded())) > 0) {
            $order->setForcedCanCreditmemo(true);
        }

        return $this;
    }
}
