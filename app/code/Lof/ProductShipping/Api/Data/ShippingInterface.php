<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\ProductShipping\Api\Data;

interface ShippingInterface
{
    const LOFSHIPPING_ID = 'lofshipping_id';
    const METHOD_NAME = 'method_name';
    const DESCRIPTION = 'description';
    const DEST_ZIP_TO = 'dest_zip_to';
    const COST = 'cost';
    const WEBSITE_ID = 'website_id';
    const WEIGHT_TO = 'weight_to';
    const CONDITIONS_SERIALIZED = 'conditions_serialized';
    const PRIORITY = 'priority';
    const DEST_REGION_ID = 'dest_region_id';
    const PRICE = 'price';
    const QUANTITY_TO = 'quantity_to';
    const DEST_ZIP = 'dest_zip';
    const FREE_SHIPPING = 'free_shipping';
    const SECOND_PRICE = 'second_price';
    const DEST_COUNTRY_ID = 'dest_country_id';
    const WEIGHT_FROM = 'weight_from';
    const QUANTITY_FROM = 'quantity_from';
    const ALLOW_SECOND_PRICE = 'allow_second_price';
    const ALLOW_FREE_SHIPPING = 'allow_free_shipping';
    const PARTNER_ID = 'partner_id';
    const SHIPPING_METHOD_ID = 'shipping_method_id';
    const PRODUCTS = 'products';
    const PRICE_FOR_UNIT = 'price_for_unit';

    /**
     * Get lofshipping_id
     * @return int|null
     */
    public function getLofshippingId();

    /**
     * Set lofshipping_id
     * @param int $lofshippingId
     * @return \Lof\ProductShipping\Api\Data\ShippingInterface
     */
    public function setLofshippingId($lofshippingId);

    /**
     * Get website_id
     * @return string|null
     */
    public function getWebsiteId();

    /**
     * Set website_id
     * @param string $websiteId
     * @return \Lof\ProductShipping\Api\Data\ShippingInterface
     */
    public function setWebsiteId($websiteId);

    /**
     * Get dest_country_id
     * @return string|null
     */
    public function getDestCountryId();

    /**
     * Set dest_country_id
     * @param string $destCountryId
     * @return \Lof\ProductShipping\Api\Data\ShippingInterface
     */
    public function setDestCountryId($destCountryId);

    /**
     * Get dest_region_id
     * @return string|null
     */
    public function getDestRegionId();

    /**
     * Set dest_region_id
     * @param string $destRegionId
     * @return \Lof\ProductShipping\Api\Data\ShippingInterface
     */
    public function setDestRegionId($destRegionId);

    /**
     * Get dest_zip
     * @return string|null
     */
    public function getDestZip();

    /**
     * Set dest_zip
     * @param string $destZip
     * @return \Lof\ProductShipping\Api\Data\ShippingInterface
     */
    public function setDestZip($destZip);

    /**
     * Get dest_zip_to
     * @return string|null
     */
    public function getDestZipTo();

    /**
     * Set dest_zip_to
     * @param string $destZipTo
     * @return \Lof\ProductShipping\Api\Data\ShippingInterface
     */
    public function setDestZipTo($destZipTo);

    /**
     * Get quantity_from
     * @return string|null
     */
    public function getQuantityFrom();

    /**
     * Set quantity_from
     * @param string $quantityFrom
     * @return \Lof\ProductShipping\Api\Data\ShippingInterface
     */
    public function setQuantityFrom($quantityFrom);

    /**
     * Get quantity_to
     * @return string|null
     */
    public function getQuantityTo();

    /**
     * Set quantity_to
     * @param string $quantityTo
     * @return \Lof\ProductShipping\Api\Data\ShippingInterface
     */
    public function setQuantityTo($quantityTo);

    /**
     * Get price
     * @return float|null
     */
    public function getPrice();

    /**
     * Set price
     * @param float $price
     * @return \Lof\ProductShipping\Api\Data\ShippingInterface
     */
    public function setPrice($price);

    /**
     * Get weight_from
     * @return string|int|float|null
     */
    public function getWeightFrom();

    /**
     * Set weight_from
     * @param string|int|float $weightFrom
     * @return \Lof\ProductShipping\Api\Data\ShippingInterface
     */
    public function setWeightFrom($weightFrom);

    /**
     * Get weight_to
     * @return string|int|float|null
     */
    public function getWeightTo();

    /**
     * Set weight_to
     * @param string|int|float $weightTo
     * @return \Lof\ProductShipping\Api\Data\ShippingInterface
     */
    public function setWeightTo($weightTo);

    /**
     * Get priority
     * @return int|null
     */
    public function getPriority();

    /**
     * Set priority
     * @param int $priority
     * @return \Lof\ProductShipping\Api\Data\ShippingInterface
     */
    public function setPriority($priority);

    /**
     * Get partner_id
     * @return int|null
     */
    public function getPartnerId();

    /**
     * Set partner_id
     * @param int $partnerId
     * @return \Lof\ProductShipping\Api\Data\ShippingInterface
     */
    public function setPartnerId($partnerId);

    /**
     * Get allow_second_price
     * @return int
     */
    public function getAllowSecondPrice();

    /**
     * Set allow_second_price
     * @param int $allowSecondPrice
     * @return \Lof\ProductShipping\Api\Data\ShippingInterface
     */
    public function setAllowSecondPrice($allowSecondPrice = 0);

    /**
     * Get second_price
     * @return float
     */
    public function getSecondPrice();

    /**
     * Set second_price
     * @param float $secondPrice
     * @return \Lof\ProductShipping\Api\Data\ShippingInterface
     */
    public function setSecondPrice($secondPrice = 0.0000);

    /**
     * Get cost
     * @return float
     */
    public function getCost();

    /**
     * Set cost
     * @param float $cost
     * @return \Lof\ProductShipping\Api\Data\ShippingInterface
     */
    public function setCost($cost = 0.0000);

    /**
     * Get allow_free_shipping
     * @return int
     */
    public function getAllowFreeShipping();

    /**
     * Set allow_free_shipping
     * @param int $allowFreeShipping
     * @return \Lof\ProductShipping\Api\Data\ShippingInterface
     */
    public function setAllowFreeShipping($allowFreeShipping = 0);

    /**
     * Get free_shipping
     * @return float
     */
    public function getFreeShipping();

    /**
     * Set free_shipping
     * @param float $freeShipping
     * @return \Lof\ProductShipping\Api\Data\ShippingInterface
     */
    public function setFreeShipping($freeShipping = 0.0000);

    /**
     * Get conditions_serialized
     * @return string|null
     */
    public function getConditionsSerialized();

    /**
     * Set conditions_serialized
     * @param string $conditionsSerialized
     * @return \Lof\ProductShipping\Api\Data\ShippingInterface
     */
    public function setConditionsSerialized($conditionsSerialized);

    /**
     * Get description
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     * @param string $description
     * @return \Lof\ProductShipping\Api\Data\ShippingInterface
     */
    public function setDescription($description);

    /**
     * Get method_name
     * @return string|null
     */
    public function getMethodName();

    /**
     * Set method_name
     * @param string $methodName
     * @return \Lof\ProductShipping\Api\Data\ShippingInterface
     */
    public function setMethodName($methodName);

    /**
     * Get shipping_method_id
     * @return int|null
     */
    public function getShippingMethodId();

    /**
     * Set shipping_method_id
     * @param int $shippingMethodId
     * @return \Lof\ProductShipping\Api\Data\ShippingInterface
     */
    public function setShippingMethodId($shippingMethodId);

    /**
     * Get price_for_unit
     * @return int|null
     */
    public function getPriceForUnit();

    /**
     * Set price_for_unit
     * @param int $price_for_unit
     * @return \Lof\ProductShipping\Api\Data\ShippingInterface
     */
    public function setPriceForUnit($price_for_unit = 1);

    /**
     * Get products
     * @return \Lof\ProductShipping\Api\Data\ProductInterface[]|mixed
     */
    public function getProductItems();

    /**
     * Set products
     * @param \Lof\ProductShipping\Api\Data\ProductInterface[]|array $products
     * @return \Lof\ProductShipping\Api\Data\ShippingInterface
     */
    public function setProductItems($products = []);
}

