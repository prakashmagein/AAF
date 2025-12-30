<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\ProductShipping\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ShippingRepositoryInterface
{

    /**
     * Save Shipping
     * @param \Lof\ProductShipping\Api\Data\ShippingInterface $shipping
     * @return \Lof\ProductShipping\Api\Data\ShippingInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Lof\ProductShipping\Api\Data\ShippingInterface $shipping
    );

    /**
     * Retrieve Shipping
     * @param int $shippingId
     * @return \Lof\ProductShipping\Api\Data\ShippingInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($shippingId);

    /**
     * Retrieve Shipping matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\ProductShipping\Api\Data\ShippingSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Shipping
     * @param \Lof\ProductShipping\Api\Data\ShippingInterface $shipping
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Lof\ProductShipping\Api\Data\ShippingInterface $shipping
    );

    /**
     * Delete Shipping by ID
     * @param int $shippingId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($shippingId);

    /**
     * Delete Shipping Method by ID
     * @param int $methodId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteMethodById($methodId);

    /**
     * Delete Shipping Method by ID
     * @param int $methodId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteShippingByMethod($methodId);

    /**
     * Get shipping method id by name
     *
     * @param string $methodName
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function calculateShippingMethodId($methodName);
}

