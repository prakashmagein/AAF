<?php
namespace Gwl\Checkoutvalidation\Block\Checkout;
class LayoutProcessor
{
    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array  $jsLayout
    ) {
            /*For shipping address form*/

            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['customer_mobile']['validation']['custom-validate-telephone'] = true;    
                   
            /*For billing address form*/

            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['billingAddress']['children']['billing-address-fieldset']['children']['customer_mobile']['validation']['custom-validate-telephone'] = true;    
          
        return $jsLayout;
    }
}