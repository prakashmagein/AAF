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
 * Class Aheadworks\RewardPoints\Observer\AddPaymentRewardPointsCardItem
 */
class AddPaymentRewardPointsCardItem implements ObserverInterface
{
    /**
     * Merge gift card amount into discount of payment checkout totals
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Payment\Model\Cart $cart */
        $cart = $observer->getEvent()->getCart();
        $salesEntity = $cart->getSalesModel();
        $value = abs((float)$salesEntity->getDataUsingMethod('base_aw_reward_points_amount'));
        if ($value > 0.0001) {
            $cart->addDiscount((double)$value);
        }
    }
}
