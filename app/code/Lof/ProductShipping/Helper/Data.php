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

namespace Lof\ProductShipping\Helper;

/**
 * ProductShipping data helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

	/**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\UrlInterface
     * */
    protected $url;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $manager;

    /**
     * @var null|array
     */
    protected $options;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

	/**
    * @param \Magento\Framework\App\Helper\Context $context
    * @param \Magento\Framework\ObjectManagerInterface $objectManager
    * @param \Magento\Customer\Model\Session $customerSession
    * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $collectionFactory
    * @param \Magento\Catalog\Model\ResourceModel\Product $product
    * @param \Magento\Store\Model\StoreManagerInterface $storeManager
    * @param \Magento\Directory\Model\Currency $currency
    */
    public function __construct(
    	\Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product $product,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\Currency $currency
    ) {
        parent::__construct($context);
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_objectManager = $objectManager;
        $this->customerSession = $customerSession;
        $this->url = $context->getUrlBuilder();
        $this->manager = $context->getModuleManager();
        $this->collectionFactory = $collectionFactory;
        $this->product = $product;
        $this->_currency = $currency;
        $this->_storeManager = $storeManager;
    }

    /**
     * get shipping is enabled or not for system config
     * @return bool|int
     */
    public function getIsActive()
    {
        return (bool)$this->_scopeConfig->getValue('carriers/lofproductshipping/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * get is disable free shipping
     *
     * @return bool|int
     */
    public function getIsDisableFreeShipping()
    {
        return (bool)$this->_scopeConfig->getValue('carriers/lofproductshipping/disable_free_shipping', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * get is enable split shipment
     *
     * @return bool|int
     */
    public function isSplitShipment()
    {
        return (bool)$this->_scopeConfig->getValue('carriers/lofproductshipping/split_shipment', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * get table rate shipping title from system config
     *
     * @return string
     */
    public function getshippingTitle()
    {
        return $this->_scopeConfig->getValue('carriers/lofproductshipping/title', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * get table rate shipping name from system config
     *
     * @return string
     */
    public function getshippingName()
    {
        return $this->_scopeConfig->getValue('carriers/lofproductshipping/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * get currency symbol
     *
     * @return string|mixed
     */
    public function getCurrencySymbol()
    {
        return $this->_currency->getCurrencySymbol();
    }

     /**
     * get shipping based on
     *
     * @return mixed
     */
    public function getShippingBasedOn()
    {
        return $this->_scopeConfig->getValue('carriers/lofproductshipping/shippingbasedon', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

    }

    /**
     * Get default shipping price
     *
     * @return float|int|null
     */
    public function getDefaultShippingPrice()
    {
        return $this->_scopeConfig->getValue('carriers/lofproductshipping/defaultprice', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get partner id
     *
     * @return int
     */
    public function getPartnerId()
    {
        $partnerId = $this->customerSession->getCustomerId();
        return $partnerId;
    }

    /**
     * get shipping config data
     *
     * @param string $field
     * @return mixed
     */
    public function getShippingConfig($field)
    {
        return $this->_scopeConfig->getValue('carriers/lofproductshipping/'.$field, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * cmp
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @return int
     */
    public function cmp($a, $b)
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
    public function santinize($str)
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
    public function inRange($val, $fromVal, $toVal)
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

}
