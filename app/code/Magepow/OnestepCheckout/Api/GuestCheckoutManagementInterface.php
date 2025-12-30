<?php
/**
 * GuestCheckoutManagementInterface
 *
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */
namespace Magepow\OnestepCheckout\Api;

/**
 * Interface for update item information
 * @api
 */
interface GuestCheckoutManagementInterface
{
    /**
     * @param string $cartId
     * @param int $itemId
     * @param int $itemQty
     * @return \Magepow\OnestepCheckout\Api\Data\DetailsInterface
     */
    public function updateItemQty($cartId, $itemId, $itemQty);

    /**
     * @param string $cartId
     * @param int $itemId
     * @return \Magepow\OnestepCheckout\Api\Data\DetailsInterface
     */
    public function removeItemById($cartId, $itemId);

    /**
     * @param string $cartId
     * @return \Magepow\OnestepCheckout\Api\Data\DetailsInterface
     */
    public function getPaymentTotalInformation($cartId);

    /**
     * @param string $cartId
     * @param bool $isUseGiftWrap
     * @return \Magepow\OnestepCheckout\Api\Data\DetailsInterface
     */
    public function updateGiftWrap($cartId, $isUseGiftWrap);

    /**
     * @param string $cartId
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

    /**
     * @param string $cartId
     * @param string $email
     * @return bool
     */
    public function saveEmailToQuote($cartId, $email);

    /**
     * Check if given email is associated with a customer account in given website.
     *
     * @param string $cartId
     * @param string $customerEmail
     * @param int $websiteId If not set, will use the current websiteId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isEmailAvailable($cartId, $customerEmail, $websiteId = null);
}
