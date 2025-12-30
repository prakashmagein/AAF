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


namespace Lof\ProductShipping\Block\Shipping;

use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Block\Product\AbstractProduct;
use Lof\ProductShipping\Model\ShippingmethodFactory;
use Magento\Directory\Model\ResourceModel\Country;
use Lof\ProductShipping\Model\ShippingFactory;

class Shipping extends AbstractProduct
{
    /**
     * @var \Magento\Framework\Data\Helper\PostHelper
     */
    protected $_postDataHelper;
    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $_urlHelper;
    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;
    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_session;
    /**
     * @var Lof\ProductShipping\Model\ShippingmethodFactory
     */
    protected $_mpshippingMethod;
    /**
     * @var \Magento\Directory\Model\ResourceModel\Country\CollectionFactory
     */
    protected $_countryCollectionFactory;
    /**
     * @var Lof\ProductShipping\Model\ShippingFactory
     */
    protected $_mpshippingModel;

    protected $request;

    /**
     * @param \Magento\Catalog\Block\Product\Context    $context
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Framework\Url\Helper\Data        $urlHelper
     * @param Customer                                  $customer
     * @param \Magento\Customer\Model\Session           $session
     * @param ShippingmethodFactory                   $shippingmethodFactory
     * @param Country\CollectionFactory                 $countryCollectionFactory
     * @param ShippingFactory                         $mpshippingModel
     * @param array                                     $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        Customer $customer,
        \Magento\Customer\Model\Session $session,
        ShippingmethodFactory $shippingmethodFactory,
        Country\CollectionFactory $countryCollectionFactory,
        ShippingFactory $mpshippingModel,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_postDataHelper = $postDataHelper;
        $this->_urlHelper = $urlHelper;
        $this->_customer = $customer;
        $this->_session = $session;
        $this->request =  $context->getRequest();
        $this->_mpshippingMethod = $shippingmethodFactory;
        $this->_countryCollectionFactory = $countryCollectionFactory;
        $this->_mpshippingModel = $mpshippingModel;
    }

    /**
     * Get shipping id from url
     *
     * @return string|int
     */
    public function getShippingId()
    {
        $path = trim($this->request->getPathInfo(), '/');
        $params = explode('/', $path);
        return end($params);
    }
    /**
     * get customer id from session
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->_session->getCustomerId();
    }

    /**
     * Get shipping by id
     *
     * @param int $shipping_id
     * @return \Lof\ProductShipping\Model\Shipping\Collection|mixed
     */
    public function getShipping($shipping_id) {
         $querydata = $this->_mpshippingModel->create()
            ->getCollection()
            ->addFieldToFilter(
                'lofshipping_id', (int)$shipping_id
            );
        return $querydata;
    }

    /**
     * get collection by partner id
     *
     * @param  int $partnerId
     * @return \Lof\ProductShipping\Model\Shipping\Collection|mixed
     */
    public function getShippingCollection($partnerId = null)
    {
        $querydata = $this->_mpshippingModel->create()
            ->getCollection()
            ->addFieldToFilter(
                'partner_id',
                ['eq' => $partnerId]
            );
        return $querydata;
    }

    /**
     * Get shipping method collection
     *
     * @return \Lof\ProductShipping\Model\Shippingmethod\Collection|mixed
     */
    public function getShippingMethodCollection()
    {
        $shippingMethodCollection = $this->_mpshippingMethod
            ->create()
            ->getCollection();
        return $shippingMethodCollection;
    }

    /**
     * Get shipping method
     *
     * @return \Lof\ProductShipping\Model\Shippingmethod
     */
    public function getShippingMethod()
    {
        return $this->_mpshippingMethod->create();
    }

    /**
     * Get shipping for shipping method
     *
     * @param int $methodId
     * @param int $partnerId
     * @return \Lof\ProductShipping\Model\Shipping\Collection|mixed
     */
    public function getShippingforShippingMethod($methodId, $partnerId)
    {
        $querydata = $this->_mpshippingModel
            ->create()
            ->getCollection()
            ->addFieldToFilter(
                'shipping_method_id',
                ['eq' => $methodId]
            )
            ->addFieldToFilter(
                'partner_id',
                ['eq' => $partnerId]
            );
        return $querydata;
    }

    /**
     * Get shipping method name
     *
     * @param int $shippingMethodId
     * @return string
     */
    public function getShippingMethodName($shippingMethodId)
    {
        $methodName = '';
        $shippingMethodModel = $this->_mpshippingMethod->create()
            ->getCollection()
            ->addFieldToFilter('entity_id', $shippingMethodId)
            ->getFirstItem();

        if ($shippingMethodModel && $shippingMethodModel->getId()) {
            $methodName = $shippingMethodModel->getMethodName();
        }
        return $methodName;
    }

    /**
     * Get country option array
     *
     * @return mixed|array
     */
    public function getCountryOptionArray()
    {
        $options = $this->getCountryCollection()
            ->setForegroundCountries($this->getTopDestinations())
            ->toOptionArray();
        $options[0]['label'] = __('Please select Country');

        return $options;
    }

    /**
     * get country collection
     *
     * @return mixed
     */
    public function getCountryCollection()
    {
        $collection = $this->_countryCollectionFactory
            ->create()
            ->loadByStore();
        return $collection;
    }

    /**
     * Retrieve list of top destinations countries.
     *
     * @return mixed|array
     */
    protected function getTopDestinations()
    {
        $destinations = (string) $this->_scopeConfig->getValue(
            'general/country/destinations',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return !empty($destinations) ? explode(',', $destinations) : [];
    }
}
