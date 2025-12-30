<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_GoogleMapPinAddress
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\GoogleMapPinAddress\Block\Checkout;

class LayoutProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface  $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Process
     *
     * @param array $result
     * @return array $result
     */
    public function process($result)
    {
        $moduleStatus = $this->scopeConfig
                             ->getValue(
                                 'googlemappinaddress/gmpa_settings/active',
                                 \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                             );
        if ($moduleStatus) {
            $result = $this->getShippingFormFields($result);
            $result = $this->getBillingFormFields($result);
            return $result;
        } else {
            return $result;
        }
    }

    /**
     * Get Additional Fields
     *
     * @return array
     */
    public function getAdditionalFields()
    {
        return [
            'latitude' => __("Latitude"),
            'longitude' => __("Longitude")
        ];
    }

    /**
     * Get Shipping Form Fields
     *
     * @param array $result
     * @return array $result
     */
    public function getShippingFormFields($result)
    {
        if (isset($result['components']['checkout']['children']['steps']['children']
                ['shipping-step']['children']['shippingAddress']['children']
                ['shipping-address-fieldset'])
        ) {
            $shippingPostcodeFields = $this->getFields('shippingAddress.custom_attributes');
            $shippingFields = $result['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset']['children'];

            if (isset($shippingFields['street'])) {
                unset($shippingFields['street']['children'][1]['validation']);
                unset($shippingFields['street']['children'][2]['validation']);
            }

            $shippingFields = array_replace_recursive($shippingFields, $shippingPostcodeFields);
            $result['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset']['children'] = $shippingFields;

        }

        return $result;
    }

    /**
     * Get Billing Form Fields
     *
     * @param array $result
     * @return array $result
     */
    public function getBillingFormFields($result)
    {
        if (isset($result['components']['checkout']['children']['steps']['children']
            ['billing-step']['children']['payment']['children']
            ['payments-list'])) {

            $paymentForms = $result['components']['checkout']['children']['steps']['children']
            ['billing-step']['children']['payment']['children']
            ['payments-list']['children'];

            foreach ($paymentForms as $paymentMethodForm => $paymentMethodValue) {

                $paymentMethodCode = str_replace('-form', '', $paymentMethodForm);

                if (!isset($result['components']['checkout']['children']['steps']['children']
                ['billing-step']['children']['payment']['children']
                ['payments-list']['children'][$paymentMethodCode . '-form'])) {
                    continue;
                }

                $billingFields = $result['components']['checkout']['children']['steps']['children']
                ['billing-step']['children']['payment']['children']
                ['payments-list']['children'][$paymentMethodCode . '-form']['children']['form-fields']['children'];

                $billingPostcodeFields = $this->getFields('billingAddress' . $paymentMethodCode .
                '.custom_attributes');

                $billingFields = array_replace_recursive($billingFields, $billingPostcodeFields);

                $result['components']['checkout']['children']['steps']['children']
                ['billing-step']['children']['payment']['children']
                ['payments-list']['children'][$paymentMethodCode . '-form']
                ['children']['form-fields']['children'] = $billingFields;
            }
        }

        return $result;
    }

    /**
     * Get Fields
     *
     * @param string $scope
     */
    public function getFields($scope)
    {
        $fields = [];
         $i = 1000;
        foreach ($this->getAdditionalFields() as $field => $label) {
            $fields[$field] = $this->getField($field, $label, $scope, $i);
            $i++;
        }
        return $fields;
    }

    /**
     * GetField function
     *
     * @param string $attributeCode
     * @param string $label
     * @param string $scope
     * @param integer $i
     * @return array
     */
    public function getField($attributeCode, $label, $scope, $i)
    {
        $defaultLat = $this->scopeConfig
                            ->getValue(
                                'googlemappinaddress/gmpa_settings/default_latitude',
                                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                            );

        $defaultLong = $this->scopeConfig
                            ->getValue(
                                'googlemappinaddress/gmpa_settings/default_longitude',
                                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                            );
        $field = [
                    'component' => 'Magento_Ui/js/form/element/abstract',
                    'config' => [
                        'customScope' => $scope,
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'ui/form/element/input',
                    ],
                    'dataScope' => $scope . '.'.$attributeCode,
                    'label' => $label,
                    'provider' => 'checkoutProvider',
                    'sortOrder' => $i,
                    'validation' => [
                        'required-entry' => true
                                ],
                    'options' => [],
                        'filterBy' => null,
                    'customEntry' => null,
                    'visible' => true,
                    'value' => ($attributeCode == 'latitude') ? $defaultLat : (
                                    ($attributeCode == 'longitude') ? $defaultLong : ''
                                )
        ];

        return $field;
    }
}
