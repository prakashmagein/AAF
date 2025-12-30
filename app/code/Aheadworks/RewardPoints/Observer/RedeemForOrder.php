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
use Magento\Framework\Event\Observer;

/**
 * Class Aheadworks\RewardPoints\Observer\RedeemForOrder
 */
class RedeemForOrder implements ObserverInterface
{
    /**
     *  {@inheritDoc}
     */
    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();
        /** @var $order \Magento\Sales\Model\Order **/
        $order = $event->getOrder();
        /** @var $quote \Magento\Quote\Model\Quote $quote */
        $quote = $event->getQuote();

        if ($quote->getAwUseRewardPoints()) {
            $order->setAwUseRewardPoints($quote->getAwUseRewardPoints());
            $order->setAwRewardPointsAmount($quote->getAwRewardPointsAmount());
            $order->setBaseAwRewardPointsAmount($quote->getBaseAwRewardPointsAmount());
            $order->setAwRewardPoints($quote->getAwRewardPoints());
            $order->setAwRewardPointsDescription($quote->getAwRewardPointsDescription());

            $order->setAwRewardPointsShippingAmount(
                $order->getExtensionAttributes()->getAwRewardPointsShippingAmount()
            );
            $order->setBaseAwRewardPointsShippingAmount(
                $order->getExtensionAttributes()->getBaseAwRewardPointsShippingAmount()
            );
            $order->setAwRewardPointsShipping(
                $order->getExtensionAttributes()->getAwRewardPointsShipping()
            );
        }
    }
}
