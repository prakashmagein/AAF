<?php
/**
 * CheckoutManagement
 *
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */

namespace Magepow\OnestepCheckout\Model;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Api\ShippingInformationManagementInterface;
use Magento\Checkout\Model\Session;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\GiftMessage\Model\GiftMessageManager;
use Magento\GiftMessage\Model\Message;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\PaymentMethodManagementInterface;
use Magento\Quote\Api\ShippingMethodManagementInterface;
use Magento\Quote\Model\Cart\ShippingMethodConverter;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\TotalsCollector;
use Magepow\OnestepCheckout\Api\CheckoutManagementInterface;
use Magepow\OnestepCheckout\Helper\Data;


class CheckoutManagement implements CheckoutManagementInterface
{
    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @type \Magepow\OnestepCheckout\Model\DetailFactory
     */
    protected $detailsFactory;

    /**
     * @var \Magento\Quote\Api\ShippingMethodManagementInterface
     */
    protected $shippingMethodManagement;

    /**
     * @var \Magento\Quote\Api\PaymentMethodManagementInterface
     */
    protected $paymentMethodManagement;

    /**
     * @var \Magento\Quote\Api\CartTotalRepositoryInterface
     */
    protected $cartTotalsRepository;

    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * Checkout session
     *
     * @type \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Checkout\Api\ShippingInformationManagementInterface
     */
    protected $shippingInformationManagement;

    /**
     * @var Data
     */
    protected $dataHelper;


    /**
     * @var DetailsFactory
     */
    protected $_detailsFactory;


    /**
     * @var Message
     */
    protected $giftMessage;

    /**
     * @var GiftMessageManager
     */
    protected $giftMessageManagement;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Quote\Model\Quote\TotalsCollector
     */
    protected $_totalsCollector;

    /**
     * @var \Magento\Quote\Api\Data\AddressInterface
     */
    protected $_addressInterface;

    /**
     * @var \Magento\Quote\Model\Cart\ShippingMethodConverter
     */
    protected $_shippingMethodConverter;

    /**
     * CheckoutManagement constructor.
     * @param CartRepositoryInterface $cartRepository
     * @param DetailsFactory $detailsFactory
     * @param ShippingMethodManagementInterface $shippingMethodManagement
     * @param PaymentMethodManagementInterface $paymentMethodManagement
     * @param CartTotalRepositoryInterface $cartTotalsRepository
     * @param UrlInterface $urlBuilder
     * @param Session $checkoutSession
     * @param ShippingInformationManagementInterface $shippingInformationManagement
     * @param Data $dataHelper
     * @param Message $giftMessage
     * @param GiftMessageManager $giftMessageManager
     * @param CustomerSession $customerSession
     * @param TotalsCollector $totalsCollector
     * @param AddressInterface $addressInterface
     * @param ShippingMethodConverter $shippingMethodConverter
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        DetailsFactory $detailsFactory,
        ShippingMethodManagementInterface $shippingMethodManagement,
        PaymentMethodManagementInterface $paymentMethodManagement,
        CartTotalRepositoryInterface $cartTotalsRepository,
        UrlInterface $urlBuilder,
        Session $checkoutSession,
        ShippingInformationManagementInterface $shippingInformationManagement,
        Data $dataHelper,
        Message $giftMessage,
        GiftMessageManager $giftMessageManager,
        customerSession $customerSession,
        TotalsCollector $totalsCollector,
        AddressInterface $addressInterface,
        ShippingMethodConverter $shippingMethodConverter
    )
    {
        $this->cartRepository                = $cartRepository;
        $this->_detailsFactory               = $detailsFactory;
        $this->shippingMethodManagement      = $shippingMethodManagement;
        $this->paymentMethodManagement       = $paymentMethodManagement;
        $this->cartTotalsRepository          = $cartTotalsRepository;
        $this->_urlBuilder                   = $urlBuilder;
        $this->checkoutSession               = $checkoutSession;
        $this->shippingInformationManagement = $shippingInformationManagement;
        $this->dataHelper                     = $dataHelper;
        $this->giftMessage                   = $giftMessage;
        $this->giftMessageManagement         = $giftMessageManager;
        $this->_customerSession              = $customerSession;
        $this->_totalsCollector              = $totalsCollector;
        $this->_addressInterface             = $addressInterface;
        $this->_shippingMethodConverter      = $shippingMethodConverter;
    }

    /**
     * {@inheritDoc}
     */
    public function updateItemQty($cartId, $itemId, $itemQty)
    {
        if ($itemQty == 0) {
            return $this->removeItemById($cartId, $itemId);
        }

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote     = $this->cartRepository->getActive($cartId);
        $quoteItem = $quote->getItemById($itemId);
        if (!$quoteItem) {
            throw new NoSuchEntityException(
                __('Cart %1 doesn\'t contain item  %2', $cartId, $itemId)
            );
        }

        try {
            $quoteItem->setQty($itemQty)->save();
            $this->cartRepository->save($quote);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not update item from quote'));
        }

        return $this->getResponseData($quote);
    }

    /**
     * {@inheritDoc}
     */
    public function removeItemById($cartId, $itemId)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote     = $this->cartRepository->getActive($cartId);
        $quoteItem = $quote->getItemById($itemId);
        if (!$quoteItem) {
            throw new NoSuchEntityException(
                __('Cart %1 doesn\'t contain item  %2', $cartId, $itemId)
            );
        }
        try {
            $quote->removeItem($itemId);
            $this->cartRepository->save($quote);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not remove item from quote'));
        }

        return $this->getResponseData($quote);
    }

    /**
     * {@inheritDoc}
     */
    public function getPaymentTotalInformation($cartId)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->cartRepository->getActive($cartId);

        return $this->getResponseData($quote);
    }

    /**
     * {@inheritDoc}
     */
    public function updateGiftWrap($cartId, $isUseGiftWrap)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->cartRepository->getActive($cartId);
        $quote->getShippingAddress()->setUsedGiftWrap($isUseGiftWrap);

        try {
            $this->cartRepository->save($quote);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not add gift wrap for this quote'));
        }

        return $this->getResponseData($quote);
    }

    /**
     * Response data to update onestepcheckout block
     *
     * @param Quote $quote
     * @return \Magepow\OnestepCheckout\Api\Data\DetailsInterface
     * @throws NoSuchEntityException
     */
    public function getResponseData(Quote $quote)
    {
        /** @var \Magepow\OnestepCheckout\Api\Data\DetailsInterface $Details */
        $Details = $this->_detailsFactory->create();

        if (!$quote->hasItems() || $quote->getHasError() || !$quote->validateMinimumAmount()) {
            $Details->setRedirectUrl($this->_urlBuilder->getUrl('checkout/cart'));
        } else {
            if ($quote->getShippingAddress()->getCountryId()) {
                $Details->setShippingMethods($this->getShippingMethods($quote));
            }
            $Details->setPaymentMethods($this->paymentMethodManagement->getList($quote->getId()));
            $Details->setTotals($this->cartTotalsRepository->get($quote->getId()));
        }

        return $Details;
    }

    /**
     * {@inheritDoc}
     */
    public function saveCheckoutInformation(
        $cartId,
        ShippingInformationInterface $addressInformation,
        $customerAttributes = [],
        $additionInformation = []
    )
    {
        try {
            $additionInformation['customerAttributes'] = $customerAttributes;
            $this->checkoutSession->setData($additionInformation);
            $this->addGiftMessage($cartId, $additionInformation);

            if ($addressInformation->getShippingAddress()) {
                if ($this->_customerSession->isLoggedIn() && isset($additionInformation['billing-same-shipping']) && !$additionInformation['billing-same-shipping']) {
                    $addressInformation->getShippingAddress()->setSaveInAddressBook(0);
                }
                $this->shippingInformationManagement->saveAddressInformation($cartId, $addressInformation);
            }
        } catch (\Exception $e) {
            throw new InputException(__('Unable to save order information. Please check input data.'));
        }

        return true;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return array
     */
    public function getShippingMethods(Quote $quote)
    {
        $result          = [];
        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->addData($this->_addressInterface->getData());
        $shippingAddress->setCollectShippingRates(true);
        $this->_totalsCollector->collectAddressTotals($quote, $shippingAddress);
        $shippingRates = $shippingAddress->getGroupedAllShippingRates();
        foreach ($shippingRates as $carrierRates) {
            foreach ($carrierRates as $rate) {
                $result[] = $this->_shippingMethodConverter->modelToDataObject($rate, $quote->getQuoteCurrencyCode());
            }
        }

        return $result;
    }

    /**
     * @param $cartId
     * @param $additionInformation
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function addGiftMessage($cartId, $additionInformation)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->cartRepository->getActive($cartId);

        if (!$this->dataHelper->isDisabledGiftMessage() && isset($additionInformation['giftMessage'])) {
            $giftMessage = dataHelper::jsonDecode($additionInformation['giftMessage']);
            $this->giftMessage->setSender(isset($giftMessage['sender']) ? $giftMessage['sender'] : '');
            $this->giftMessage->setRecipient(isset($giftMessage['recipient']) ? $giftMessage['recipient'] : '');
            $this->giftMessage->setMessage(isset($giftMessage['message']) ? $giftMessage['message'] : '');
            $this->giftMessageManagement->setMessage($quote, 'quote', $this->giftMessage);
        }
    }
}
