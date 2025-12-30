<?php
/**
 * Copyright © Gwl All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Gwl\OrderDetails\Block\Checkout\LayoutProcessor;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;

/**
 * Class NewAddressAttribute
 */
class NewAddressAttribute implements LayoutProcessorInterface
{
    CONST BILLING_TYPE = 'billingAddress';
    CONST SHIPPING_TYPE = 'shippingAddress';
    CONST ADDRESS_ATTRIBUTE_CODE = 'district';
    CONST ATTRIBUTE_LABEL = 'District';

    CONST ADDRESS_ATTRIBUTE_CODE_2 = 'house_description';
    CONST ATTRIBUTE_LABEL_2 = 'House Description';

    /**
     * @param $jsLayout
     * @return array
     */
    public function process($jsLayout): array
    {
        //Build shipping address field
        $jsLayout['components']['checkout']['children']['steps']
        ['children']['shipping-step']['children']['shippingAddress']
        ['children']['shipping-address-fieldset']['children'][self::ADDRESS_ATTRIBUTE_CODE] = $this->getCustomField(self::SHIPPING_TYPE);

        $jsLayout['components']['checkout']['children']['steps']
        ['children']['shipping-step']['children']['shippingAddress']
        ['children']['shipping-address-fieldset']['children'][self::ADDRESS_ATTRIBUTE_CODE_2] = $this->getCustomField2(self::SHIPPING_TYPE);

        //Build billing address field
        foreach ($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'] as $key => $payment)
        {
            $paymentCode = self::BILLING_TYPE.str_replace('-form','',$key);
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children'][self::ADDRESS_ATTRIBUTE_CODE] = $this->getCustomField($paymentCode);

            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children'][self::ADDRESS_ATTRIBUTE_CODE_2] = $this->getCustomField2($paymentCode);

        }

        return $jsLayout;
    }

    /**
     * @param string $type
     * @return array
     */
    private function getCustomField(string $type): array
    {
        return [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                // customScope is used to group elements within a single form (e.g. they can be validated separately)
                'customScope' => $type . '.custom_attributes',
                'customEntry' => null,
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input',
            ],
            'dataScope' => $type . '.custom_attributes' . '.' . self::ADDRESS_ATTRIBUTE_CODE,
            'label' => self::ATTRIBUTE_LABEL,
            'provider' => 'checkoutProvider',
            'sortOrder' => 101,
            'validation' => [
                'required-entry' => false
            ],
            'options' => [],
            'filterBy' => null,
            'customEntry' => null,
            'visible' => true,
            'value' => '' // value field is used to set a default value of the attribute
        ];
    }


    /**
     * @param string $type
     * @return array
     */
    private function getCustomField2(string $type): array
    {
        return [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                // customScope is used to group elements within a single form (e.g. they can be validated separately)
                'customScope' => $type . '.custom_attributes',
                'customEntry' => null,
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input',
            ],
            'dataScope' => $type . '.custom_attributes' . '.' . self::ADDRESS_ATTRIBUTE_CODE_2,
            'label' => self::ATTRIBUTE_LABEL_2,
            'provider' => 'checkoutProvider',
            'sortOrder' => 109,
            'validation' => [
                'required-entry' => false
            ],
            'options' => [],
            'filterBy' => null,
            'customEntry' => null,
            'visible' => true,
            'value' => '' // value field is used to set a default value of the attribute
        ];
    }
}
