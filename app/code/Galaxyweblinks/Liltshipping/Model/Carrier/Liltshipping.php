<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Galaxyweblinks\Liltshipping\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;
use Galaxyweblinks\Liltshipping\Model\CustomFactory;
use Magento\Checkout\Model\Cart;
use Magento\Catalog\Api\ProductRepositoryInterface;

class Liltshipping extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{

    protected $_code = 'liltshipping';

    protected $_isFixed = true;

    protected $_rateResultFactory;

    protected $_rateMethodFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param ProductRepositoryInterface $productRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        CustomFactory $customFactory,
        ProductRepositoryInterface $productRepository,
        Cart $cart,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->customFactory = $customFactory;
        $this->cart = $cart;
        $this->productRepository = $productRepository;
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
            return false;
        }
        
        $free_cities = $this->getConfigData('custom_shipping_city_field');
        $cities = explode(",", $free_cities);

        $result = $this->_rateResultFactory->create();


        $products = $this->cart->getQuote()->getAllItems();



        $shippingPrice_pro = 0;
        $liltcount=0;
        foreach ($products as $product) {
            $proid = $product->getProductId();
            $product_deatils = $this->productRepository->getById($proid);
            $lilt_shipping = $product_deatils->getData('lilt_shipping');
            $lilt_shipping_price = $product_deatils->getData('lilt_shipping_price') ? $product_deatils->getData('lilt_shipping_price') : 0;

            if($lilt_shipping == 1){
                $liltcount++;
                $shippingPrice_pro =  intval($shippingPrice_pro) + intval($lilt_shipping_price);
            }
        }

        if($liltcount == 0){   
            return false;
        }


        if ($shippingPrice !== false && $shippingPrice_pro == 0) {
            $shippingPrice = false;
            $method = $this->_rateMethodFactory->create();
            

            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod($this->_code);
            $method->setMethodTitle($this->getConfigData('name'));

            // if ($request->getFreeShipping() === true) {
            //     $shippingPrice = '0.00';
            // }

            if($customer_city != null){
                foreach ($cities as $value) {

                    
                    if (strtolower($value) == strtolower($customer_city)) {
                        $shippingPrice = $this->getConfigData('price') ? $this->getConfigData('price')   : 0;

                    }
                    foreach ($data as $item) {
                            $transcity = $item['transcity'];
                            $city = $item['city'];
                            
                            if($value == $city ){
                                if($transcity == $customer_city ){
                                    $shippingPrice = $this->getConfigData('price') ? $this->getConfigData('price')   : 0;
                                }
                            }
                        }
                    
                }

            }

            if ($shippingPrice === false) {
                return false;
            }



            $method->setPrice($shippingPrice);
            $method->setCost($shippingPrice);

            $result->append($method);
        }
        else{

            $shippingPriceprod = false;
            $method = $this->_rateMethodFactory->create();

            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod($this->_code);
            $method->setMethodTitle($this->getConfigData('name'));

            if($customer_city != null){
                foreach ($cities as $value) {

                    
                    if (strtolower($value) == strtolower($customer_city)) {
                        $shippingPriceprod = true;

                    }
                    foreach ($data as $item) {
                            $transcity = $item['transcity'];
                            $city = $item['city'];
                            
                            if($value == $city ){
                                if($transcity == $customer_city ){
                                    $shippingPriceprod = true;
                                }
                            }
                        }
                    
                }

            }

            if ($shippingPriceprod === false) {
                return false;
            }

            $method->setPrice($shippingPrice_pro);
            $method->setCost($shippingPrice_pro);

            $result->append($method);




        }


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
