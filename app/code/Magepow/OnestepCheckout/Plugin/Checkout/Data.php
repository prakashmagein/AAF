<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Osc
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Magepow\OnestepCheckout\Plugin\Checkout;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magepow\OnestepCheckout\Helper\Data as OscData;

/**
 * Class Data
 * @package Magepow\OnestepCheckout\Plugin\Checkout
 */
class Data
{
    /**
     * @var OscData
     */
    private $helper;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * Data constructor.
     *
     * @param OscData $helper
     * @param Session $checkoutSession
     */
    public function __construct(OscData $helper, Session $checkoutSession)
    {
        $this->helper = $helper;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param \Magento\Checkout\Helper\Data $subject
     * @param $result
     *
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterIsAllowedGuestCheckout(\Magento\Checkout\Helper\Data $subject, $result)
    {
        if (!($quote = $this->checkoutSession->getQuote()) || !$this->helper->isEnabled()) {
            return $result;
        }

        return (bool)$this->helper->getAllowGuestCheckout($quote);
    }
}
