<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\OrderEdit\Plugin\Backend\Magento\Sales\Block\Adminhtml\Order\Create\Shipping\Method;

use Magento\Sales\Block\Adminhtml\Order\Create\Shipping\Method\Form as Subject;
use Magefan\OrderEdit\Model\Quote\Manager as QuoteManager;

class Form
{
    /**
     * @var QuoteManager
     */
    protected $quoteManager;

    /**
     * @param QuoteManager $quoteManager
     */
    public function __construct(
        QuoteManager $quoteManager
    ) {
        $this->quoteManager = $quoteManager;
    }

    /**
     * @param Subject $subject
     * @param $result
     * @return mixed
     */
    public function afterGetShippingRates(Subject $subject, $result)
    {
        if ('mforderedit_order_loadBlock' !== $subject->getRequest()->getFullActionName()
            && 'mforderedit_order_edit' !== $subject->getRequest()->getFullActionName()) {
            return $result;
        }

        $this->setCustomPriceToSelectedShippingRate($subject, $result);

        return $result;
    }

    /**
     * @param $subject
     * @param $shippingRateGroups
     * @return void
     */
    private function setCustomPriceToSelectedShippingRate($subject, $shippingRateGroups): void
    {
        foreach ($shippingRateGroups as $_rates) {
            foreach ($_rates as $rate) {
                if ($subject->isMethodActive($rate->getCode())) {

                    if ($this->isRequestFromEditShippingForm($subject)) {
                        $customPrice = (string)$subject->getRequest()->getParam('mf_custom_shipping_price');
                    } else {
                        $customPrice = (string)$subject->getQuote()->getData('mf_custom_shipping_price');
                    }


                    if ('' !== $customPrice) {
                        $customPrice = (float)$customPrice;
                        $baseCustomPrice = $customPrice;

                        if ($customPrice) {
                            $priceRate = $this->quoteManager->priceCurrency->convert($customPrice, $this->quoteManager->_getQuote()->getStore()) / $customPrice;
                            $baseCustomPrice = $customPrice / $priceRate;
                        }

                        $rate->setPrice($baseCustomPrice);
                    }

                    return;
                }
            }
        }
    }

    /**
     * @return bool
     */
    private function isRequestFromEditShippingForm($subject): bool
    {
        $order = (array)$subject->getRequest()->getParam('order');
        if (isset($order['shipping_method'])) {
            return true;
        }

        return false;
    }
}
