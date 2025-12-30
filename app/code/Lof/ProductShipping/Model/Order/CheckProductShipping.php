<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);
namespace Magento\Shipping\Controller\Adminhtml\Order;

use Magento\Framework\DataObject;
use Lof\ProductShipping\Model\Carrier;
use Magento\Quote\Model\ResourceModel\Quote\Address\Rate\CollectionFactory as RateCollectionFactory;

/**
 * CheckProductShipping
 *
 */
class CheckProductShipping extends DataObject
{
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Lof\ProductShipping\Model\ShippingFactory
     */
    protected $shippingFactory;

    /**
     * @var \Lof\ProductShipping\Model\ShippingmethodFactory
     */
    protected $shippingMethodFactory;

    /**
     * @var RateCollectionFactory
     */
    protected $rateCollectionFactory;

    /**
     * @var \Magento\Sales\Model\Order\ItemFactory
     */
    protected $orderItemFactory;

    /**
     * @var \Lof\ProductShipping\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\Sales\Model\Order[]
     */
    protected $_orderData = [];

    /**
     * @var int[]
     */
    protected $_productShippingMethodIds = [];

    /**
     * @param \Lof\ProductShipping\Model\ShippingFactory $shippingFactory
     * @param \Lof\ProductShipping\Model\ShippingmethodFactory $shippingMethodFactory
     * @param \Lof\ProductShipping\Helper\Data $helperData
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Sales\Model\Order\ItemFactory $orderItemFactory
     * @param RateCollectionFactory $rateCollectionFactory
     * @param array $data = []
     */
    public function __construct(
        \Lof\ProductShipping\Model\ShippingFactory $shippingFactory,
        \Lof\ProductShipping\Model\ShippingmethodFactory $shippingMethodFactory,
        \Lof\ProductShipping\Helper\Data $helperData,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\Order\ItemFactory $orderItemFactory,
        RateCollectionFactory $rateCollectionFactory,
        array $data = []
    ) {
        $this->shippingFactory = $shippingFactory;
        $this->orderFactory = $orderFactory;
        $this->orderItemFactory = $orderItemFactory;
        $this->rateCollectionFactory = $rateCollectionFactory;
        $this->shippingMethodFactory = $shippingMethodFactory;
        $this->helperData = $helperData;
        parent::__construct($data);
    }
    /**
     * Initialize shipment model instance
     *
     * @return $this
     */
    public function load()
    {
        return $this;
    }

    /**
     * Get order
     *
     * @param int $orderId
     * @return \Magento\Sales\Model\Order
     */
    protected function getOrder(int $orderId): \Magento\Sales\Model\Order
    {
        if (!isset($this->_orderData[$orderId])) {
            $this->_orderData[$orderId] = $this->orderFactory->create()->load($orderId);
        }
        return $this->_orderData[$orderId];
    }

    /**
     * Init
     *
     * @param int $orderId
     * @return mixed
     */
    public function getAvailableShipping($orderId)
    {
        $splits = [];
        $order = $this->getOrder($orderId);
        $productShippingIds = $this->getProductShippingAddress($order);

        foreach ($order->getAllItems() as $orderItem) {
            $quantity = (float)$orderItem->getQtyOrdered();
            $shippedQty = (float)$orderItem->getQtyShipped();
            $quantity = $quantity - $shippedQty;
            if ($quantity > 0) {
                $itemId = $orderItem->getId();
                $parentId = $orderItem->getParentItemId();
                $productId = $orderItem->getProductId();

                $foundRecord = $this->shippingFactory->create()->getCollection()
                                    ->addProductToFilter($productId)
                                    ->addFieldToFilter("lofshipping_id", ["in" => $productShippingIds])
                                    ->getFirstItem();
                if ($foundRecord && $foundRecord->getId()) {
                    if (!isset($splits[$foundRecord->getId()])) {
                        $splits[$foundRecord->getId()] = [];
                        $splits[$foundRecord->getId()]["method"] = $this->getShippingMethodName((int)$foundRecord->getShippingMethodId());
                        $splits[$foundRecord->getId()]["rate"] = $this->calculateShippingRate($foundRecord, $quantity);
                        $splits[$foundRecord->getId()]["items"] = [];
                    }
                    $splits[$foundRecord->getId()]["items"][$itemId] = $quantity;
                }
            }
        }
        return $splits;
    }

    /**
     * Get product shipping address
     *
     * @param mixed $order
     * @return mixed
     */
    public function getProductShippingAddress($order)
    {
        if (count($this->_productShippingMethodIds) <=0 ) {
            $address = $order->getShippingAddress();
            $quoteRate = $this->rateCollectionFactory->create()
                                ->addFieldToFilter("address_id", $address->getQuoteAddressId())
                                ->addFieldToFilter("carrier", Carrier::CODE)
                                ->getFirstItem();
            if ($quoteRate && $quoteRate->getMethodDescription()) {
                $methodDescription = $quoteRate->getMethodDescription();
                $methodDescription = str_replace("methods:", "", $methodDescription);
                $this->_productShippingMethodIds = $methodDescription ? explode("|", $methodDescription) : [];
            }
        }
        return $this->_productShippingMethodIds;
    }

    /**
     * get shipping method name
     *
     * @param int $shippingMethodId
     * @return string
     */
    public function getShippingMethodName($shippingMethodId)
    {
        if ($shippingMethodId) {
            $method = $this->shippingMethodFactory->create()->load($shippingMethodId);
            return $method->getMethodName();
        }
        return "";
    }

    /**
     * Calculate shipping rate for product
     *
     * @param \Lof\ProductShipping\Model\Shipping|mixed
     * @param int $quantity
     * @return float|int
     */
    public function calculateShippingRate($shippingRate, $quantity = 0)
    {
        $rate = 0;
        if ($quantity) {
            $isPriceForUnit = (int)$shippingRate->getPriceForUnit();
            $rate = (float)$shippingRate->getPrice();
            if ($isPriceForUnit) {
                $secondPrice = (float)$shippingRate->getSecondPrice();
                if ($shippingRate->getAllowSecondPrice() && $secondPrice > 0 && $quantity > 1) {
                    $rate += $secondPrice*($quantity-1);
                } else {
                    $rate = $rate * $quantity;
                }
            }
        }
        return $rate;
    }

}
