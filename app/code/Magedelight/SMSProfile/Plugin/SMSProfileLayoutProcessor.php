<?php
/**
 * Magedelight
 * Copyright (C) 2022 Magedelight <info@magedelight.com>
 *
 * @category  Magedelight
 * @package   Magedelight_SMSProfile
 * @copyright Copyright (c) 2022 Mage Delight (http://www.magedelight.com/)
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author    Magedelight <info@magedelight.com>
 */
 
namespace Magedelight\SMSProfile\Plugin;

use Magedelight\SMSProfile\Helper\Data as HelperData;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\SessionFactory;

class SMSProfileLayoutProcessor
{
    /**
     * @var HelperData
     */
    private $helper;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**  @var SessionFactory */
    private $customerSession;

    /**
     * LayoutProcessor constructor.
     * @param HelperData $helper
     * @param SessionFactory $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        HelperData $helper,
        SessionFactory $customerSession,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->helper = $helper;
        $this->customerSession = $customerSession->create();
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array $jsLayout
    ) {
        $mobile ='';
        if ($this->customerSession->isLoggedIn()) {
            $id = $this->customerSession->getId();
            $customer =$this->customerRepository->getById($id);
            if ($customer->getCustomAttribute('customer_mobile')) {
                $countryCode = $customer->getCustomAttribute('countryreg') ? $customer->getCustomAttribute('countryreg')->getValue() : "";
                $mobile = $countryCode.$customer->getCustomAttribute('customer_mobile')->getValue();
            }
        }
        if ($this->helper->getModuleStatus()) {
            if (isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
                ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'])) {
                /*if ($this->helper->isCustomerCountryEnabled()) {
                    $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
                    ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']
                    ['telephone']['validation'] = ['required-entry' => true,
                    "min_tel_digit_length" =>  $this->helper->getSmsProfilePhoneMinLength(), "max_tel_digit_length" =>  $this->helper->getSmsProfilePhoneMaxLength() ];
                } else {
                    $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
                    ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']
                    ['telephone']['validation'] = ['required-entry' => true,
                    "validate-number"=>true,"min_tel_digit_length" =>  $this->helper->getSmsProfilePhoneMinLength(), "max_tel_digit_length" =>  $this->helper->getSmsProfilePhoneMaxLength() ];
                }*/
                if (!$this->helper->isCustomerCountryEnabled()) {
                    $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
                    ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']
                    ['telephone']['validation'] = ['required-entry' => true,
                    "validate-number"=>true,/*"min_tel_digit_length" =>  $this->helper->getSmsProfilePhoneMinLength(), "max_tel_digit_length" =>  $this->helper->getSmsProfilePhoneMaxLength()*/ ];
                }

                if ($this->helper->getPhoneNote() && !$this->helper->isCustomerCountryEnabled()) {
                    $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
                    ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']
                    ['telephone']['notice'] = $this->helper->getPhoneNote();
                }

                if ($mobile != '') {
                    $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
                    ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']
                    ['telephone']['value'] = $mobile;
                }
                if ($this->helper->isCustomerCountryEnabled()) {
                    $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
                        ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']
                        ['telephone'] = $this->helper->telephoneFieldConfig("shippingAddress");
                }
            }

            /* config: checkout/options/display_billing_address_on = payment_method */
            if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children'])) {
                foreach ($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                         ['payment']['children']['payments-list']['children'] as $key => $payment) {
                        /* telephone */
                    if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children'] ['payment']['children']['payments-list']['children'][$key]['children']['form-fields'])) {
                        /*if ($this->helper->isCustomerCountryEnabled()) {
                            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                            ['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']
                            ['telephone']['validation'] = ['required-entry' => true, "min_tel_digit_length" => $this->helper->getSmsProfilePhoneMinLength(), "max_tel_digit_length" =>  $this->helper->getSmsProfilePhoneMaxLength() ];
                        } else {
                            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                            ['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']
                            ['telephone']['validation'] = ['required-entry' => true,"validate-number"=>true, "min_tel_digit_length" => $this->helper->getSmsProfilePhoneMinLength(), "max_tel_digit_length" =>  $this->helper->getSmsProfilePhoneMaxLength() ];
                        }*/

                        if (!$this->helper->isCustomerCountryEnabled()) {
                            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                            ['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']
                            ['telephone']['validation'] = ['required-entry' => true,"validate-number"=>true, /*"min_tel_digit_length" => $this->helper->getSmsProfilePhoneMinLength(), "max_tel_digit_length" =>  $this->helper->getSmsProfilePhoneMaxLength()*/ ];
                        }

                        if ($this->helper->getPhoneNote()) {
                            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                            ['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']
                            ['telephone']['notice'] = $this->helper->getPhoneNote();
                        }

                        if ($mobile != '') {
                            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                            ['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']
                            ['telephone']['value'] = $mobile;
                        }

                        if ($this->helper->isCustomerCountryEnabled()) {
                            $method = str_replace('-form', '', $key);
                            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                            ['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']
                            ['telephone'] = $this->helper->telephoneFieldConfig("billingAddress", $method);
                        }
                    }
                }
            }
        }
        return $jsLayout;
    }
}
