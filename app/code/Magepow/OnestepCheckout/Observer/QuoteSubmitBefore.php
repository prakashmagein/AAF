<?php
/**
 * QuoteSubmitBefore
 *
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */
namespace Magepow\OnestepCheckout\Observer;

use Magento\Checkout\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class QuoteSubmitBefore implements ObserverInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;
    protected $orderComment;
    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @codeCoverageIgnore
     */
    public function __construct(Session $checkoutSession)
    {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();
        
        $oscData = $this->checkoutSession->getOnestepCheckoutData();
        if (isset($oscData['deliveryComment'])) {
            $order->setData('onestepcheckout_order_comment', $oscData['deliveryComment']);
        }

        if (isset($oscData['deliveryTime'])) {
            $order->setData('onestepcheckout_delivery_time', $oscData['deliveryTime']);
        }
        if (isset($oscData['houseSecurityCode'])) {
            $order->setData('onestepcheckout_order_house_security_code', $oscData['houseSecurityCode']);
        }

        $address = $quote->getShippingAddress();
        if ($address->getUsedGiftWrap() && $address->hasData('onestepcheckout_gift_wrap_amount') && $address->getUsedGiftWrap()) {
            $order->setData('gift_wrap_type', $address->getGiftWrapType())
                ->setData('onestepcheckout_gift_wrap_amount', $address->getOscGiftWrapAmount())
                ->setData('base_onestepcheckout_gift_wrap_amount', $address->getBaseOscGiftWrapAmount());

            foreach ($order->getItems() as $item) {
                $quoteItem = $quote->getItemById($item->getQuoteItemId());
                if ($quoteItem && $quoteItem->hasData('onestepcheckout_gift_wrap_amount')) {
                    $item->setData('onestepcheckout_gift_wrap_amount', $quoteItem->getOscGiftWrapAmount())
                        ->setData('base_onestepcheckout_gift_wrap_amount', $quoteItem->getBaseOscGiftWrapAmount());
                }
            }
        }
    }
}
