<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_ProductShipping
 * @copyright  Copyright (c) 2022 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */
declare(strict_types=1);

namespace Lof\ProductShipping\Model;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Unserialize\Unserialize;
use Magento\Checkout\Model\Session;
use Magento\Shipping\Model\Rate\Result;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Galaxyweblinks\Liltshipping\Model\CustomFactory;

class Carrier extends AbstractCarrier implements CarrierInterface
{
    /**
     * Code of the carrier.
     *
     * @var string
     */
    const CODE = 'lofproductshipping';

    /**
    * @var Session
    */
    protected $checkoutSession;

    /**
     * Code of the carrier.
     *
     * @var string
     */
    protected $_code = self::CODE;

    /**
     * Rate request data.
     *
     * @var \Magento\Quote\Model\Quote\Address\RateRequest|null
     */
    protected $_request;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * Rate result data.
     *
     * @var Result|null
     */
    protected $_result;

    /**
     * @var Unserialize
     */
    protected $_unserialize;

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @var mixed|array
     */
    protected $_errors = [];

    /**
     * Flag if response is for shipping label creating
     *
     * @var bool
     */
    protected $_isShippingLabelFlag = false;

    /**
     * Shipping rates result
     *
     * @var mixed|array
     */
    protected $_rates = [];

    /**
     * @var int[]
     */
    protected $_existItems = [];

    /**
     * @var mixed
     */
    protected $_productShippingRate = [];

    /**
     * @var int[]
     */
    protected $_foundShippingRate = [];

    /**
     * @var \Lof\ProductShipping\Model\ShippingFactory
     */
    protected $shippingFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Carrier Construct
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param ProductFactory $productFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Lof\ProductShipping\Model\ShippingFactory $shippingFactory
     * @param Unserialize $unserialize
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param ProductRepositoryInterface $productRepository
     * @param array $data = []
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        ProductFactory $productFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Lof\ProductShipping\Model\ShippingFactory $shippingFactory,
        Unserialize $unserialize,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        ProductRepositoryInterface $productRepository,
        CustomFactory $customFactory,
        array $data = []
    )
    {
        $this->productFactory = $productFactory;
        $this->checkoutSession = $checkoutSession;
        $this->_objectManager = $objectManager;
        $this->shippingFactory = $shippingFactory;
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_unserialize = $unserialize;
        $this->productRepository = $productRepository;
        $this->customFactory = $customFactory;
        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $data
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedMethods()
    {
        return ['lofproductshipping' => $this->getConfigData('name')];
    }

    /**
     * getShippingPricedetail
     *
     * @param RateRequest $request
     * @return mixed
     */
    public function collectRates(RateRequest $request)
    {        
        //$product = $this->productFactory->create();
        if (!$this->getConfigData('active')) {
            return false;
        }

        $customer_city = $request->getDestCity();

        $custom = $this->customFactory->create();
        $data = $custom->getCityAndSelectedFieldDetails();

        if (!$this->getConfigData('custom_shipping_city_field')) {
            return false;
        }
        
        $free_cities = $this->getConfigData('custom_shipping_city_field');
        $cities = explode(",", $free_cities);


        $result = $this->_rateResultFactory->create();
        $partner = 0;
        $methodDescription = "";
        //$handling = 0;
        $countrycode = $request->getDestCountryId();
        $region = $request->getDestRegionCode();
        $postcode = $request->getDestPostcode();
        $postcode = !empty($postcode) ? str_replace('-', '', $postcode) : "";
        $shippingdetail = array();
        $shipPostalInfo = array('countrycode' => $countrycode, 'regioncode' => $region, 'postalcode' => $postcode);

        $this->_isShippingLabelFlag = true;

        foreach ($request->getAllItems() as $item) {
            $proid = $item->getProductId();
            //$options = $item->getProductOptions();

            if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                continue;
            }
            $child_weight = 0;
            $weight = 0;
            if ($item->getHasChildren()) {
                //$_product = $this->productFactory->create()->load($item->getProductId());
                $_product = $this->productRepository->getById($proid);


                if ($_product->getTypeId() == "bundle" || $_product->getTypeId() == "configurable") {
                    foreach ($item->getChildren() as $child) {

                        $productWeight = $_product->getData('weight');
                        //$productWeight = $this->productFactory->create()->load($child->getProductId())->getWeight();
                        $child_weight += $productWeight * $child->getQty();
                    }
                    $weight = $child_weight * $item->getQty();
                }
            } else {

                $product1 = $this->productRepository->getById($proid);
                $productWeight = $product1->getData('weight'); //'10'; //$this->productFactory->create()->load($proid)->getWeight();
                $weight = $productWeight * $item->getQty();
            }
            if (count($shippingdetail) == 0) {
                $shippingdetail[] = [
                    "seller_id" => $partner,
                    "items_weight" => $weight,
                    "product_name" => $item->getName(),
                    "item_id" => $item->getId()
                ];
            } else {
                $shipinfoflag = true;
                $index = 0;
                foreach ($shippingdetail as $itemship) {

                    $itemship['items_weight'] = $itemship['items_weight'] + $weight;
                    $itemship['product_name'] = $itemship['product_name'] . "," . $item->getName();
                    $itemship['item_id'] = $itemship['item_id'] . "," . $item->getId();
                    $shippingdetail[$index] = $itemship;
                    $shipinfoflag = false;

                    $index++;
                }
                if ($shipinfoflag == true) {
                    $shippingdetail[] = [
                        "items_weight" => $weight,
                        "product_name" => $item->getName(),
                        "item_id" => $item->getId()
                    ];
                }
            }
            $partner = 0;
        }
        $shippingpricedetail = $this->getShippingPricedetail($shippingdetail, $shipPostalInfo);
        
        if (!$shippingpricedetail) {
            return false;
        }
        if ($shippingpricedetail['errormsg'] !== "") {
            // Display error message if there
            $this->_errors[$this->_code] = $shippingpricedetail['errormsg'];
            $error = $this->_rateErrorFactory->create();
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData("title"));
            $error->setErrorMessage($shippingpricedetail['errormsg']);
            return $error;
        }
        if ($this->_foundShippingRate) {
            $methodDescription = "methods:".implode("|", $this->_foundShippingRate);
        }
        /*store shipping in session*/
        $shippingAll = $this->checkoutSession->getShippingInfo();
        $shippingAll[$this->_code] = $shippingpricedetail['shippinginfo'];
        $this->checkoutSession->setShippingInfo($shippingAll);
        /*store shipping in session*/
        $method = $this->_rateMethodFactory->create();
        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData("title"));
        /* Use method name */
        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData("name"));
        $method->setMethodDescription($methodDescription);



        // custom code for the city
        $iscity = 0;
        if($customer_city != null){
                foreach ($cities as $value) {
                    if (strtolower($value) == strtolower($customer_city)) {
                        $iscity = 1;
                    }
                    foreach ($data as $item) {
                            $transcity = $item['transcity'];
                            $city = $item['city'];
                            if($value == $city ){
                                if($transcity == $customer_city ){
                                    $iscity = 1;
                                }
                            }
                        }
                }

            }


        if ($iscity == 0) {
            return false; 
        }    
           


        $method->setCost($shippingpricedetail['cost']);
        $method->setPrice($shippingpricedetail['handlingfee']);
        $result->append($method);

        return $result;
    }

    /**
     * getShippingPricedetail
     *
     * @param mixed $shippingdetail
     * @param mixed $shipPostalInfo
     * @return mixed|bool
     */
    public function getShippingPricedetail($shippingdetail, $shipPostalInfo)
    {
        $submethod = array();
        $shippinginfo = array();
        $msg = "";
        $handling = 0;
        $totalCost = 0;
        $shippingbasedon = $this->getConfigData("shippingbasedon");
        $allItems = $this->checkoutSession->getQuote()->getAllItems();
        //$countAllItems = count($allItems);
        $quote = $this->checkoutSession->getQuote();
        $quoteData = $quote->getData();
        $subtotal = isset($quoteData['subtotal'])?$quoteData['subtotal']:0;
        $flag = false;



        foreach ($shippingdetail as $shipdetail) {
            if (!isset($shipdetail['item_id']) || empty($shipdetail['item_id'])) {
                continue;
            }
            
            $price = 0;
            $cost = 0;
            $itemsarray = explode(',', $shipdetail['item_id']);
            $similarShippingMethods = [];
            
            foreach ($allItems as $item) {


                
                $bundlePrice = 0;
                if (in_array($item->getId(), $itemsarray)) {
                    if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                        continue;
                    }

                    if ($item->getHasChildren()) {
                        $_product = $this->productRepository->getById($item->getProductId());
                        //$_product = $this->productFactory->create()->load($item->getProductId());
                        if ($_product->getTypeId() == "bundle") {
                            $shippingInfo = $this->getShippingCharge($shipPostalInfo, $item, $subtotal);
                            
                            if ($shippingInfo) {
                                $flag = true;
                                $mpshippingcharge = $shippingInfo["first_item_fee"];
                                $secondFee = $shippingInfo["second_item_fee"];
                                $cost = $cost+$shippingInfo["cost"];
                                $itemQty = floatval($item->getQty());

                                if ($shippingbasedon == 0) {

                                    if ($shippingInfo["price_for_unit"]) {
                                        if ($itemQty > 1) {
                                            $mpshippingcharge = ($mpshippingcharge * 1) + ($secondFee * ($itemQty - 1));
                                        } else {
                                            $mpshippingcharge = ($mpshippingcharge * $itemQty);
                                        }
                                    }

                                    $price = $price + $mpshippingcharge;

                                    if (!isset($similarShippingMethods[$shippingInfo["method_id"]])) {
                                        $similarShippingMethods[$shippingInfo["method_id"]] = [];
                                    }
                                    $similarShippingMethods[$shippingInfo["method_id"]][] = [
                                        "item" => $item,
                                        "shippingInfo" => $shippingInfo,
                                        "newPrice" => $mpshippingcharge
                                    ];

                                    continue;
                                } else {
                                    foreach ($item->getChildren() as $child) {
                                        $shippingInfo = $this->getShippingCharge($shipPostalInfo, $child, $subtotal);
                                        if ($shippingInfo) {
                                            $flag = true;
                                            $mpshippingcharge = $shippingInfo["first_item_fee"];
                                            $secondFee = $shippingInfo["second_item_fee"];
                                            $cost = $cost+$shippingInfo["cost"];
                                            $itemQty = floatval($child->getQty());
                                            if ($shippingInfo["price_for_unit"]) {
                                                if ($itemQty > 1) {
                                                    $mpshippingcharge = ($mpshippingcharge * 1) + ($secondFee * ($itemQty - 1));
                                                } else {
                                                    $mpshippingcharge = ($mpshippingcharge * $itemQty);
                                                }
                                            }

                                            $price = $price + $mpshippingcharge;

                                            if (!isset($similarShippingMethods[$shippingInfo["method_id"]])) {
                                                $similarShippingMethods[$shippingInfo["method_id"]] = [];
                                            }
                                            $similarShippingMethods[$shippingInfo["method_id"]][] = [
                                                "item" => $child,
                                                "shippingInfo" => $shippingInfo,
                                                "newPrice" => $mpshippingcharge
                                            ];
                                        }
                                    }
                                    $bundlePrice = $bundlePrice * floatval($item->getQty());
                                    $price = $price + $bundlePrice;
                                }
                            }
                        } else if ($_product->getTypeId() == "configurable") {
 
                                    
                            
                            if ($shippingbasedon == 0) {
                                $shippingInfo = $this->getShippingCharge($shipPostalInfo, $item, $subtotal);

                                if ($shippingInfo) {
                                    $flag = true;
                                    $mpshippingcharge = $shippingInfo["first_item_fee"];
                                    $secondFee = $shippingInfo["second_item_fee"];
                                    $cost = $cost+$shippingInfo["cost"];
                                    $itemQty = floatval($item->getQty());
                                    if ($shippingInfo["price_for_unit"]) {
                                        if ($itemQty > 1) {
                                            $mpshippingcharge = ($mpshippingcharge * 1) + ($secondFee * ($itemQty - 1));
                                        } else {
                                            $mpshippingcharge = ($mpshippingcharge * $itemQty);
                                        }
                                    }

                                    $price = $price + $mpshippingcharge;

                                    if (!isset($similarShippingMethods[$shippingInfo["method_id"]])) {
                                        $similarShippingMethods[$shippingInfo["method_id"]] = [];
                                    }
                                    $similarShippingMethods[$shippingInfo["method_id"]][] = [
                                        "item" => $item,
                                        "shippingInfo" => $shippingInfo,
                                        "newPrice" => $mpshippingcharge
                                    ];

                                
                                }
                                continue;
                            } else {


                                foreach ($item->getChildren() as $child) {

                                    
                                    $shippingInfo = $this->getShippingCharge($shipPostalInfo, $child, $subtotal);
       
                                
                                    if ($shippingInfo) {
                                        $flag = true;
                                        $mpshippingcharge = $shippingInfo["first_item_fee"];
                                        $secondFee = $shippingInfo["second_item_fee"];
                                        $cost = $cost+$shippingInfo["cost"];
                                        $itemQty = floatval($child->getQty());
                                        if ($shippingInfo["price_for_unit"]) {
                                            if ($itemQty > 1) {
                                                $mpshippingcharge = ($mpshippingcharge * 1) + ($secondFee * ($itemQty - 1));
                                            } else {
                                                $mpshippingcharge = ($mpshippingcharge * $itemQty);
                                            }
                                        }

                                        $price = $price + $mpshippingcharge;

                                        if (!isset($similarShippingMethods[$shippingInfo["method_id"]])) {
                                            $similarShippingMethods[$shippingInfo["method_id"]] = [];
                                        }
                                        $similarShippingMethods[$shippingInfo["method_id"]][] = [
                                            "item" => $child,
                                            "shippingInfo" => $shippingInfo,
                                            "newPrice" => $mpshippingcharge
                                        ];
                                    }
                                    continue;
                                }
                            }
                        }
                    } else {
                        $shippingInfo = $this->getShippingCharge($shipPostalInfo, $item, $subtotal);


                        if ($shippingInfo) {
                            $flag = true;
                            $mpshippingcharge = $shippingInfo["first_item_fee"];
                            $secondFee = $shippingInfo["second_item_fee"];
                            $cost = $cost+$shippingInfo["cost"];
                            $itemQty = floatval($item->getQty());
                            //$newPrice = $mpshippingcharge;
                            if ($shippingInfo["price_for_unit"]) {
                                if ($itemQty > 1) {
                                    $mpshippingcharge = ($mpshippingcharge * 1) + ($secondFee * ($itemQty - 1));
                                } else {
                                    $mpshippingcharge = ($mpshippingcharge * $itemQty);
                                }
                            }


                            $price = $price + $mpshippingcharge;


                            if (!isset($similarShippingMethods[$shippingInfo["method_id"]])) {
                                $similarShippingMethods[$shippingInfo["method_id"]] = [];
                            }
                            $similarShippingMethods[$shippingInfo["method_id"]][] = [
                                "item" => $item,
                                "shippingInfo" => $shippingInfo,
                                "newPrice" => $mpshippingcharge
                            ];

                           
                        }
                    }
                }
            }

   


            $price = $this->calculateRateBaseOnWeight($similarShippingMethods, $price, $subtotal);


            $handling = $handling + $price;
            $totalCost = $totalCost + $cost;
            

            $submethod = [];
            $submethod[] = [
                "method" => $this->getConfigData("title"),
                "cost" => $cost,
                "error" => 0
            ];
            //$submethod = array(array('method' => $this->getConfigData("title"), 'cost' => $cost, 'error' => 0));
            $shippinginfo[] = [
                'methodcode' => $this->_code,
                'shipping_ammount' => $price,
                'product_name' => $shipdetail['product_name'],
                'submethod' => $submethod,
                'item_ids' => $shipdetail['item_id']
            ];
           //array_push($shippinginfo, array('methodcode' => $this->_code, 'shipping_ammount' => $price, 'product_name' => $shipdetail['product_name'], 'submethod' => $submethod, 'item_ids' => $shipdetail['item_id']));
        }
        $msg = '';

        if ($flag){
            return array(
                'cost' => $totalCost,
                'handlingfee' => $handling,
                'shippinginfo' => $shippinginfo,
                'errormsg' => $msg
            );
        }
        return false;
    }

    /**
     * proccessAdditionalValidation
     *
     * @param \Magento\Framework\DataObject $request
     * @return bool
     */
    public function proccessAdditionalValidation(\Magento\Framework\DataObject $request)
    {
        return true;
    }

    /**
     * getShippingCharge
     *
     * @param mixed $shipPostalInfo
     * @param mixed $quoteItem
     * @param float|int $subtotal
     * @return mixed
     */
    public function getShippingCharge($shipPostalInfo, $quoteItem, $subtotal = 0)
    {
        $ret = 0;
        $secondRet = 0;
        $cost = 0;
        $priceForUnit = 1;
        $methodId = 0;
        $rateId = 0;
        //FIRST GET DEFAULT SHIPPING PRICE SPECIFIED FOR EACH INDIVIDUAL PRODUCT
        $product1 = $this->productRepository->getById($quoteItem->getProductId());

        $ret = $product1->getData('lof_shipping_charge') ? $product1->getData('lof_shipping_charge') : 0;

        $flow_shipping = $product1->getData('flow_shipping');    

        if($flow_shipping == "0"){
            $ret = null;
        }

        //$ret = $product->getLofShippingCharge();
        $ret = is_numeric($ret)?floatval($ret):null;
        $secondRet = $ret;

        //IF NOTHING RETURN FROM ABOVE THEN GET THE SHIPPING CHARGE FFROM ANY MATCHED ITEM IN THE lof_sp
        if ($ret == 0 || $ret == null || $ret == "") {
            $shippingChanges = $this->getShippingChargeFromLof($shipPostalInfo, $quoteItem, $subtotal);
            
            if ($shippingChanges) {
                $ret = $shippingChanges["first_item_fee"];
                $secondRet = $shippingChanges["second_item_fee"];
                $cost = $shippingChanges["cost"];
                $priceForUnit = $shippingChanges["price_for_unit"];
                $methodId = $shippingChanges["method_id"];
                $rateId = $shippingChanges["rate_id"];
            }
             
        }
        
        //IF NOTHING RETURN FROM ABOVE THEN GET DEFAULT SHIPPING PRICE SPECIFIED BY THE LOF_SP METHOD FOR ALL PRODUCT
        if ($ret == 0 && $ret != "") {
            $ret = $this->getConfigData("defaultprice");
            $secondRet = $ret;
        }

        if (!in_array($quoteItem->getProductId(), $this->_existItems)) {
            $this->_existItems[] = $quoteItem->getProductId();
        }
        if ($secondRet == "" || $secondRet == null || !is_numeric($secondRet)) {
            return false;
        } 

        return [
            "first_item_fee" => floatval($ret),
            "second_item_fee" => floatval($secondRet),
            "cost" => floatval($cost),
            "price_for_unit" => $priceForUnit,
            "method_id" => $methodId,
            "rate_id" => $rateId
        ];
    }

    /**
     * cmp
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @return int
     */
    private function cmp($a, $b)
    {
        if ($a == $b) {
            return 0;
        }
        return ($a["priority"] < $b["priority"]) ? -1 : 1;
    }

    /**
     * Santinize
     *
     * @param string|null
     * @return string
     */
    private function santinize($str)
    {
        if ($str == null) $str = "";
        $str = is_numeric($str) ? ("".$str) : $str;
        return strtoupper(trim($str));
    }

    /**
     * In range
     *
     * @param mixed|string $val
     * @param mixed|string $fromVal
     * @param mixed|string $toVal
     * @return bool
     */
    private function inRange($val, $fromVal, $toVal)
    {
        //Nothing to check -> consider to be in range(some products have eight quantity or weight attribute)
        if ($val === "" || $val === "*") return true;
        if (!is_numeric($val)) return false;

        $ret = true;

        $val = intval($val);

        if ($fromVal != "*") {
            $fromVal = intval($fromVal);
            if ($val < $fromVal) $ret = false;
        }
        if ($toVal != "*") {
            $toVal = intval($toVal);
            if ($val > $toVal) $ret = false;
        }

        return $ret;
    }

    /**
     * Calculate base on weight
     *
     * @param mixed $similarShippingMethods
     * @param float|int $totalPrice
     * @param float|int $subtotal
     * @return float|int
     */
    public function calculateRateBaseOnWeight($similarShippingMethods, $totalPrice, $subtotal = 0)
    {
        $total_subPrice = 0;
    
        $price = $totalPrice;
        foreach ($similarShippingMethods as $_methodId => $methodItem) {
            $totalWeight = 0;
            $totalQty = 0;
            $subPrice = 0;
            
            if (count($methodItem) >= 1) {
                foreach ($methodItem as $_item) {
                    $totalWeight += (float)$_item["item"]->getRowWeight();
                    $totalQty += (int)$_item["item"]->getQty();
                    $subPrice += (float)$_item["newPrice"];
                    //if (($foundRateId = array_search($_item["shippingInfo"]["rate_id"], $this->_foundShippingRate)) !== false) {
                    //    unset($this->_foundShippingRate[$foundRateId]);
                    //}            
                }
                

                $total_subPrice +=$subPrice;

                
            }
            if ($totalWeight > 0 && $totalQty > 0) {

                $foundRatePrice = $this->getShippingRateByIdAndWeight($_methodId, $totalWeight, $totalQty, $subtotal);


                     //my updates
                     //$price = $price - $subPrice + $foundRatePrice;//remove old rate price and calculate new rate price for same method id
                     
                     $price =  $total_subPrice;// + $foundRatePrice;//remove old rate price and calculate new rate price for same method id
            }
        }

    

        return $price;


    }

    /**
     * Get shipping rate
     *
     * @param int $_methodId
     * @param float|int $totalWeight
     * @param float|int $totalQty
     * @param float|int $subtotal
     * @return float|int
     */
    public function getShippingRateByIdAndWeight($_methodId, $totalWeight, $totalQty, $subtotal = 0)
    {

        
        $price = 0;
        $weight = $this->santinize($totalWeight);
        $quantity = $this->santinize($totalQty);

        $shippingCollection = $this->shippingFactory->create()->getCollection();
        $shippingCollection->addFieldToFilter("shipping_method_id", $_methodId);
        $select = $shippingCollection->getSelect();
        $conn = $shippingCollection->getConnection();
        $shippingArr = $conn->fetchAll($select);

        $applicableShippingArray = [];

        foreach ($shippingArr as $shippingCandidate) {
            //If quantity does not match the range then skip this shippinCandidate
            $weightFrom = $this->santinize($shippingCandidate["weight_from"]);
            $weightTo = $this->santinize($shippingCandidate["weight_to"]);
            if (!$this->inRange($weight, $weightFrom, $weightTo)) continue;

            //If quantity does not match the range then skip this shippinCandidate
            $quantityFrom = $this->santinize($shippingCandidate["quantity_from"]);
            $quantityTo = $this->santinize($shippingCandidate["quantity_to"]);
            if (!$this->inRange($quantity, $quantityFrom, $quantityTo)) continue;

            $applicableShippingArray[] = $shippingCandidate;
        }

        if (!empty($applicableShippingArray) && count($applicableShippingArray) > 0) {
            //Sort the array base on the priority
            usort($applicableShippingArray, array("Lof\ProductShipping\Model\Carrier", "cmp"));

            //Take the first item as the highest priority (1: is the highest)
            $firstRate = $applicableShippingArray[0];

            $price = $firstRate["price"];

            if ($firstRate["price_for_unit"]) {
                $price *= $totalQty;
            }
            //if (!in_array($firstRate["lofshipping_id"], $this->_foundShippingRate)) {
            //    $this->_foundShippingRate[] = $firstRate["lofshipping_id"];
            //}
            //Apply free shipping
            if ((int)$firstRate['allow_free_shipping'] && floatval($firstRate['free_shipping']) && floatval($firstRate['free_shipping']) <= $subtotal) {
                $price = 0;
            }
        }
        return $price;
    }

    /**
     * getShippingChargeFromLof
     *
     * @param mixed $shipPostalInfo
     * @param mixed $quoteItem
     * @param float|int $subtotal
     * @return mixed
     */
    public function getShippingChargeFromLof($shipPostalInfo, $quoteItem, $subtotal = 0)
    {
        $ret = 0;
        $secondRet = 0;
        $cost = 0;
        $priceForUnit = 1;
        $methodId = 0;
        $rateId = 0;
        //FIRST GET THE SHIPPING CHARGE FFROM ANY MATCHED ITEM IN THE lof_sp
        $countryCode = $this->santinize($shipPostalInfo["countrycode"]);
        $regionCode = $this->santinize($shipPostalInfo["regioncode"]);
        $zipCode = $this->santinize($shipPostalInfo["postalcode"]);

        $productId = $this->santinize($quoteItem->getProductId());
        $weight = $this->santinize($quoteItem->getWeight());
        $rowWeight = $this->santinize($quoteItem->getRowWeight());
        $quantity = $this->santinize($quoteItem->getQty());

        $applicableShippingArray = [];
        //Select all shippings that cofigued to deal with this product

        $shippingCollection = $this->shippingFactory->create()->getCollection();

        $select = $shippingCollection->getSelect()->join(
            $shippingCollection->getTable("lof_ps_rate_product"),
            "lof_ps_rate_product.lofshipping_id = main_table.lofshipping_id");

        //Todo UN-COMMENT OUT this line
        $select->where("lof_ps_rate_product.product_id  = ?", (int)$productId);

        $conn = $shippingCollection->getConnection();
        $shippingArr = $conn->fetchAll($select);

        foreach ($shippingArr as $shippingCandidate) {
            //It country code does not match the skip this shippinCandidate
            $cc = $this->santinize($shippingCandidate["dest_country_id"]);
            if ($cc != $countryCode) continue;

            //It region code EXIST (not empty) for BOTH side (except the case the regionCode="*") then they should be match; if they do not match then skip this shippinCandidate
            $rc = $this->santinize($shippingCandidate["dest_region_id"]);
            if (!empty($regionCode) && $rc != "*" && !empty($rc) && ($rc != $regionCode)) continue;

            //If zicode does not match the range then skip this shippinCandidate
            $zipFrom = $this->santinize($shippingCandidate["dest_zip"]);
            $zipTo = $this->santinize($shippingCandidate["dest_zip_to"]);
            if (!$this->inRange($zipCode, $zipFrom, $zipTo)) continue;

            //If weight does not match the range then skip this shippinCandidate
            $weightFrom = $this->santinize($shippingCandidate["weight_from"]);
            $weightTo = $this->santinize($shippingCandidate["weight_to"]);
            $checkWeight = $weight;
            if (!$shippingCandidate["price_for_unit"]) {
                $checkWeight = $rowWeight;
            }
            if (!$this->inRange($checkWeight, $weightFrom, $weightTo)) continue;

            //If quantity does not match the range then skip this shippinCandidate
            $quantityFrom = $this->santinize($shippingCandidate["quantity_from"]);
            $quantityTo = $this->santinize($shippingCandidate["quantity_to"]);
            if (!$this->inRange($quantity, $quantityFrom, $quantityTo)) continue;
            $applicableShippingArray[] = $shippingCandidate;
        }

        //Todo important COMMENT THIS LINE OUT
        //$applicableShippingArray = $shippingArr;

        if (!empty($applicableShippingArray) && count($applicableShippingArray) > 0) {
            //Sort the array base on the priority
            usort($applicableShippingArray, array("Lof\ProductShipping\Model\Carrier", "cmp"));

            //Take the first item as the highest priority (1: is the highest)
            $firstRate = $applicableShippingArray[0];
            if (!isset($this->_productShippingRate[$quoteItem->getProductId()])) {
                $this->_productShippingRate[$quoteItem->getProductId()] = $firstRate;
            }
            $ret = $firstRate["price"];
            if ((int)$firstRate["allow_second_price"]) {
                $secondRet = $firstRate["second_price"];
            } else {
                $secondRet = $ret;
            }

            if (!in_array($firstRate["lofshipping_id"], $this->_foundShippingRate)) {
                $this->_foundShippingRate[] = $firstRate["lofshipping_id"];
            }

            $methodId = $firstRate["shipping_method_id"];
            $rateId = $firstRate["lofshipping_id"];
            $cost = $firstRate["cost"];
            $priceForUnit = isset($firstRate["price_for_unit"]) ? (int)$firstRate["price_for_unit"] : $priceForUnit;
            //Apply free shipping
            if ((int)$firstRate['allow_free_shipping'] && floatval($firstRate['free_shipping']) && floatval($firstRate['free_shipping']) <= $subtotal) {
                $ret = 0;
                $secondRet = 0;
            }
        }

        return [
            "first_item_fee" => floatval($ret),
            "second_item_fee" => floatval($secondRet),
            "cost" => floatval($cost),
            "price_for_unit" => $priceForUnit,
            "method_id" => $methodId,
            "rate_id" => $rateId
        ];
    }
}
