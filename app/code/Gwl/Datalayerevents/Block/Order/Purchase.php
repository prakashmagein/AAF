<?php
declare(strict_types=1);

namespace Gwl\Datalayerevents\Block\Order;

use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;

class Purchase extends \Magento\Framework\View\Element\Template
{

    /**
     * @var null
     */
    protected $order;


    /**
     * @var Session
     */
    protected $checkoutSession;

    protected $serializer;

    protected $categoryCollectionFactory;

    protected $productRepository;
    
    /**
     * @param Context $context
     * @param Data $dataHelper
     * @param Config $config
     * @param Attribute $attribute
     * @param Session $checkoutSession
     * @param DataItem $additionalData
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        SerializerInterface $serializer,
        CollectionFactory $categoryCollectionFactory,
        ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->checkoutSession = $checkoutSession;
        $this->serializer = $serializer;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->productRepository = $productRepository;
    }

    /**
     * Get item
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException|\Zend_Db_Statement_Exception
     */
    public function getItemsEvent()
    {
        $order = $this->getOrder();
        $items = [];
        $index = 1;
        foreach ($order->getItems() as $key => $item) {
            $product = $this->productRepository->getById($item->getProductId());

            $categoryIds = $product->getCategoryIds();
            $categoryNames = $this->getCategory($categoryIds);

            $data['item_name'] = $item->getName();
            $data['item_id'] = $item->getProductId();
            $data['original_price'] = (float)($item->getPrice());
            $data["price"] = (float) $product->getPrice();
            $data['discount'] = (float) ($product->getPrice() - $item->getPrice());
            $data['quantity'] = (float)$item->getQtyOrdered();
            $data['currency'] = $this->getCurrency();
            $data['item_brand'] = '';
            $data['item_category'] = $categoryNames;
            $data['item_list_name'] = '';
            $data['item_list_id'] = '';
            $data['item_url'] = $product->getProductUrl();
            $data['index'] = $index;
            $items[] = $data;

            $index++;
        }
        return $items;
    }

    /**
     * Get order
     *
     * @return array|mixed|string|null
     */
    public function getOrder()
    {
        if ($this->order) {
            return $this->order;
        }
        $order = $this->getData('order');
        if ($order) {
            return $order;
        }
        return $this->checkoutSession->getLastRealOrder();
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue()
    {
        $order = $this->getOrder();
        
        return (float)$order->getGrandTotal();
    }

    /**
     * Get transaction id
     *
     * @return mixed
     */
    public function getTransactionId()
    {
        $order = $this->getOrder();
        return $order->getIncrementId();
    }

    /**
     * Get shipping amount
     *
     * @return float
     */
    public function getShippingAmount()
    {
        return (float)$this->getOrder()->getShippingAmount();
    }

    /**
     * Serialize item
     *
     * @param array $item
     * @return bool|string
     */
    public function serializeItem($item)
    {
        return $this->serializer->serialize($item);
    }

    /**
     * Get tax amount
     *
     * @return float
     */
    public function getTaxAmount()
    {
        return (float)$this->getOrder()->getTaxAmount();
    }

    /**
     * Get coupon code
     *
     * @return mixed
     */
    public function getCouponCode()
    {
        return $this->getOrder()->getCouponCode();
    }

    /**
     * Get currency
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrency()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $store = $this->_storeManager->getStore($storeId);
        return $store->getCurrentCurrencyCode();
    }

    /**
     * Get affiliation
     *
     * @return string|null
     */
    public function getAffiliation()
    {
        $code = $this->config->getItemAffiliation();
        if ($code) {
            return $this->attribute->getAttributeLabelByCode($code);
        }
        return '';
    }

    /**
     * Escaper.
     *
     * @return \Magento\Framework\Escaper
     */
    public function escaper()
    {
        return $this->_escaper;
    }

    public function getCategory($categoryIds){
        $categoryCollection = $this->categoryCollectionFactory->create()
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('entity_id', ['in' => $categoryIds]);

        $categoryNames = [];

        foreach ($categoryCollection as $category) {
            $categoryNames[] = $category->getName();
        }

        return $categoryNames;
    }

    public function getAppliedGiftCards()
    {
        return $this->getOrder()->getGiftCards();

    }

    public function getPaymentMethod()
    {
        return $this->getOrder()->getPayment()->getMethod();
    }
    
}
