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

use Lof\ProductShipping\Api\Data\ShippingInterface;
use Lof\ProductShipping\Api\Data\ProductInterfaceFactory;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Shipping extends AbstractModel implements ShippingInterface, IdentityInterface
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    /**
     * CMS page cache tag
     */
    const CACHE_TAG = 'lofproductShipping';

    /**
     * @var string
     */
    protected $_cacheTag = 'lofproductShipping';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'lofproductShipping';

    /**
     * @var ProductInterfaceFactory
     */
    protected $productFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ProductInterfaceFactory $productFactory,
        ResourceModel\Shipping $resource = null,
        ResourceModel\Shipping\Collection $resourceCollection = null,
        array $data = []
    ) {
        $this->productFactory = $productFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lof\ProductShipping\Model\ResourceModel\Shipping');
    }
    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getLofshippingId()];
    }

    public function getYesNo()
    {
        return [
            self::STATUS_ENABLED  => __('Allow'),
            self::STATUS_DISABLED => __('Dont Use')
        ];
    }

    /**
     * get product ids
     */
    public function getProductIds()
    {
        return $this->getResource()->getProductIds($this->getEntityId());
    }

    /**
     * @inheritDoc
     */
    public function getLofshippingId()
    {
        return $this->getData(self::LOFSHIPPING_ID);
    }

    /**
     * @inheritDoc
     */
    public function setLofshippingId($lofshippingId)
    {
        return $this->setData(self::LOFSHIPPING_ID, $lofshippingId);
    }

    /**
     * @inheritDoc
     */
    public function getWebsiteId()
    {
        return $this->getData(self::WEBSITE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setWebsiteId($websiteId)
    {
        return $this->setData(self::WEBSITE_ID, $websiteId);
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @inheritDoc
     */
    public function getDestCountryId()
    {
        return $this->getData(self::DEST_COUNTRY_ID);
    }

    /**
     * @inheritDoc
     */
    public function setDestCountryId($destCountryId)
    {
        return $this->setData(self::DEST_COUNTRY_ID, $destCountryId);
    }

    /**
     * @inheritDoc
     */
    public function getDestRegionId()
    {
        return $this->getData(self::DEST_REGION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setDestRegionId($destRegionId)
    {
        return $this->setData(self::DEST_REGION_ID, $destRegionId);
    }

    /**
     * @inheritDoc
     */
    public function getDestZip()
    {
        return $this->getData(self::DEST_ZIP);
    }

    /**
     * @inheritDoc
     */
    public function setDestZip($destZip)
    {
        return $this->setData(self::DEST_ZIP, $destZip);
    }

    /**
     * @inheritDoc
     */
    public function getDestZipTo()
    {
        return $this->getData(self::DEST_ZIP_TO);
    }

    /**
     * @inheritDoc
     */
    public function setDestZipTo($destZipTo)
    {
        return $this->setData(self::DEST_ZIP_TO, $destZipTo);
    }

    /**
     * @inheritDoc
     */
    public function getQuantityFrom()
    {
        return $this->getData(self::QUANTITY_FROM);
    }

    /**
     * @inheritDoc
     */
    public function setQuantityFrom($quantityFrom)
    {
        return $this->setData(self::QUANTITY_FROM, $quantityFrom);
    }

    /**
     * @inheritDoc
     */
    public function getQuantityTo()
    {
        return $this->getData(self::QUANTITY_TO);
    }

    /**
     * @inheritDoc
     */
    public function setQuantityTo($quantityTo)
    {
        return $this->setData(self::QUANTITY_TO, $quantityTo);
    }

    /**
     * @inheritDoc
     */
    public function getPrice()
    {
        return $this->getData(self::PRICE);
    }

    /**
     * @inheritDoc
     */
    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
    }

    /**
     * @inheritDoc
     */
    public function getWeightFrom()
    {
        return $this->getData(self::WEIGHT_FROM);
    }

    /**
     * @inheritDoc
     */
    public function setWeightFrom($weightFrom)
    {
        return $this->setData(self::WEIGHT_FROM, $weightFrom);
    }

    /**
     * @inheritDoc
     */
    public function getWeightTo()
    {
        return $this->getData(self::WEIGHT_TO);
    }

    /**
     * @inheritDoc
     */
    public function setWeightTo($weightTo)
    {
        return $this->setData(self::WEIGHT_TO, $weightTo);
    }

    /**
     * @inheritDoc
     */
    public function getPriority()
    {
        return $this->getData(self::PRIORITY);
    }

    /**
     * @inheritDoc
     */
    public function setPriority($priority)
    {
        return $this->setData(self::PRIORITY, $priority);
    }

    /**
     * @inheritDoc
     */
    public function getPartnerId()
    {
        return $this->getData(self::PARTNER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setPartnerId($partnerId)
    {
        return $this->setData(self::PARTNER_ID, $partnerId);
    }

    /**
     * @inheritDoc
     */
    public function getAllowSecondPrice()
    {
        return $this->getData(self::ALLOW_SECOND_PRICE);
    }

    /**
     * @inheritDoc
     */
    public function setAllowSecondPrice($allowSecondPrice = 0)
    {
        return $this->setData(self::ALLOW_SECOND_PRICE, $allowSecondPrice);
    }

    /**
     * @inheritDoc
     */
    public function getSecondPrice()
    {
        return $this->getData(self::SECOND_PRICE);
    }

    /**
     * @inheritDoc
     */
    public function setSecondPrice($secondPrice = 0.0000)
    {
        return $this->setData(self::SECOND_PRICE, $secondPrice);
    }

    /**
     * @inheritDoc
     */
    public function getCost()
    {
        return $this->getData(self::COST);
    }

    /**
     * @inheritDoc
     */
    public function setCost($cost = 0.0000)
    {
        return $this->setData(self::COST, $cost);
    }

    /**
     * @inheritDoc
     */
    public function getAllowFreeShipping()
    {
        return $this->getData(self::ALLOW_FREE_SHIPPING);
    }

    /**
     * @inheritDoc
     */
    public function setAllowFreeShipping($allowFreeShipping = 0)
    {
        return $this->setData(self::ALLOW_FREE_SHIPPING, $allowFreeShipping);
    }

    /**
     * @inheritDoc
     */
    public function getFreeShipping()
    {
        return $this->getData(self::FREE_SHIPPING);
    }

    /**
     * @inheritDoc
     */
    public function setFreeShipping($freeShipping = 0.0000)
    {
        return $this->setData(self::FREE_SHIPPING, $freeShipping);
    }

    /**
     * @inheritDoc
     */
    public function getConditionsSerialized()
    {
        return $this->getData(self::CONDITIONS_SERIALIZED);
    }

    /**
     * @inheritDoc
     */
    public function setConditionsSerialized($conditionsSerialized)
    {
        return $this->setData(self::CONDITIONS_SERIALIZED, $conditionsSerialized);
    }

    /**
     * @inheritDoc
     */
    public function getMethodName()
    {
        return $this->getData(self::METHOD_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setMethodName($methodName)
    {
        return $this->setData(self::METHOD_NAME, $methodName);
    }

    /**
     * @inheritDoc
     */
    public function getShippingMethodId()
    {
        return $this->getData(self::SHIPPING_METHOD_ID);
    }

    /**
     * @inheritDoc
     */
    public function setShippingMethodId($shippingMethodId)
    {
        return $this->setData(self::SHIPPING_METHOD_ID, $shippingMethodId);
    }

    /**
     * @inheritDoc
     */
    public function getPriceForUnit()
    {
        return $this->getData(self::PRICE_FOR_UNIT);
    }

    /**
     * @inheritDoc
     */
    public function setPriceForUnit($price_for_unit = 1)
    {
        return $this->setData(self::PRICE_FOR_UNIT, $price_for_unit);
    }

    /**
     * @inheritDoc
     */
    public function getProductItems()
    {
        $productItems = $this->getData(self::PRODUCTS);
        if (!$productItems) {
            $products = $this->getData("products");
            if ($products) {
                $items = [];
                foreach ($products as $_product) {
                    $productModel = $this->productFactory->create();
                    $productModel->setProductId((int)$_product['product_id']);
                    $productModel->setPosition((int)$_product['position']);
                    $productModel->setLofshippingId((int)$_product['lofshipping_id']);
                    $items[] = $productModel;
                }
                $this->setProductItems($items);
            }
        }
        return $this->getData(self::PRODUCTS);
    }

    /**
     * @inheritDoc
     */
    public function setProductItems($products = [])
    {
        return $this->setData(self::PRODUCTS, $products);
    }
}
