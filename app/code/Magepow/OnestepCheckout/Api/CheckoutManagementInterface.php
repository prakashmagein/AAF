<?php
/**
 * CheckoutManagementInterface
 *
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */
namespace Magepow\OnestepCheckout\Api;

interface CheckoutManagementInterface
{
    /**
     * @param int $cartId
     * @param int $itemId
     * @param int $itemQty
     * @return \Magepow\OnestepCheckout\Api\Data\DetailsInterface
     */
    public function updateItemQty($cartId, $itemId, $itemQty);

    /**
     * @param int $cartId
     * @param int $itemId
     * @return \Magepow\OnestepCheckout\Api\Data\DetailsInterface
     */
    public function removeItemById($cartId, $itemId);

    /**
     * @param int $cartId
     * @return \Magepow\OnestepCheckout\Api\Data\DetailsInterface
     */
    public function getPaymentTotalInformation($cartId);

    /**
     * @param int $cartId
     * @param bool $isUseGiftWrap
     * @return \Magepow\OnestepCheckout\Api\Data\DetailsInterface
     */
    public function updateGiftWrap($cartId, $isUseGiftWrap);

    /**
     * @param int $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     * @param string[] $customerAttributes
     * @param string[] $additionInformation
     * @return bool
     */
    public function saveCheckoutInformation(
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation,
        $customerAttributes = [],
        $additionInformation = []
    );
}
