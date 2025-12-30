<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Galaxyweblinks\FreeShipping\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;
use Galaxyweblinks\Liltshipping\Model\CustomFactory;

class Freeshipping extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{

    protected $_code = 'freeshipping';

    protected $_isFixed = true;

    protected $_rateResultFactory;

    protected $_rateMethodFactory;


    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        CustomFactory $customFactory,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->customFactory = $customFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $customer_city = $request->getDestCity();
        $custom = $this->customFactory->create();
        $data = $custom->getCityAndSelectedFieldDetails();
        $shippingPrice = $this->getConfigData('price');
        if (!$this->getConfigData('custom_shipping_city_field')) {
           // return false;
        }
        $free_cities = $this->getConfigData('custom_shipping_city_field');
        $cities = explode(",", $free_cities);
        $result = $this->_rateResultFactory->create();

        $check_shipping_city = false;
        $method = $this->_rateMethodFactory->create();

        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('name'));
/*
            if($customer_city != null){
                foreach ($cities as $value) {

                    
                    if (strtolower($value) == strtolower($customer_city)) {
                        $check_shipping_city = true;

                    }
                    foreach ($data as $item) {
                            $transcity = $item['transcity'];
                            $city = $item['city'];
                            
                            if($value == $city ){
                                if($transcity == $customer_city ){
                                    $check_shipping_city = true;
                                }
                            }
                        }
                    
                }

            }

            if ($check_shipping_city === false) {
                return false;
            }
*/
            $method->setPrice(0);
            $method->setCost(0);
            $result->append($method);
            return $result;
    }

    /**
     * getAllowedMethods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }
}

