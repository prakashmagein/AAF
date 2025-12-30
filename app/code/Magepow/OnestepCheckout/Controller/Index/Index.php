<?php
/**
 * Index
 *
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */
namespace Magepow\OnestepCheckout\Controller\Index;

use Magento\Checkout\Controller\Onepage;

class Index extends Onepage
{
    /**
     * @type \Magepow\OnestepCheckout\Helper\Data
     */
    protected $_checkoutHelper;

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $this->_checkoutHelper = $this->_objectManager->get(\Magepow\OnestepCheckout\Helper\Data::class);
        if (!$this->_checkoutHelper->isEnabled()) {
            $this->messageManager->addError(__('One step checkout is turned off.'));

            return $this->resultRedirectFactory->create()->setPath('checkout');
        }

        $quote = $this->getOnepage()->getQuote();
        if (!$this->_customerSession->isLoggedIn() && !$this->_checkoutHelper->getAllowGuestCheckout($quote)) {
            $this->messageManager->addWarningMessage(__('Guest checkout is disabled. You need to log in to an existing account or create a new account to continue making purchases.'));

            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }
        
        if (!$quote->hasItems() || $quote->getErrors() || !$quote->validateMinimumAmount()) {
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }

        $this->_customerSession->regenerateId();
        $this->_objectManager->get('Magento\Checkout\Model\Session')->setCartWasUpdated(false);
        $this->getOnepage()->initCheckout();

        $this->initDefaultMethods($quote);

        $resultPage    = $this->resultPageFactory->create();
        $checkoutTitle = $this->_checkoutHelper->getCheckoutTitle();
        $resultPage->getConfig()->getTitle()->set($checkoutTitle);
        $resultPage->getConfig()->setPageLayout($this->_checkoutHelper->isShowHeaderFooter() ? '1column' : 'checkout');

        return $resultPage;
    }

    /**
     * @param $quote
     * @return bool
     */
    public function initDefaultMethods($quote)
    {
        $shippingAddress = $quote->getShippingAddress();

        $defaultCountryId = $this->getDefaultCountryFromLocale();
        if (!$shippingAddress->getCountryId()) {
           if (!empty($this->_checkoutHelper->getDefaultCountryId())) {
                $defaultCountryId = $this->_checkoutHelper->getDefaultCountryId();
            }
            $shippingAddress->setCountryId($defaultCountryId)->setCollectShippingRates(true);
        }
        $method = null;
        try {
            $availableMethods = $this->_objectManager->get(\Magento\Quote\Api\ShippingMethodManagementInterface::class)
                ->getList($quote->getId());
            if (sizeof($availableMethods) == 1) {
                $method = array_shift($availableMethods);
            } else if (!$shippingAddress->getShippingMethod() && sizeof($availableMethods)) {
                $defaultMethod = array_filter($availableMethods, [$this, 'filterMethod']);
                if (sizeof($defaultMethod)) {
                    $method = array_shift($defaultMethod);
                }
            }

            if ($method) {
                $methodCode = $method->getCarrierCode() . '_' . $method->getMethodCode();
                $this->getOnepage()->saveShippingMethod($methodCode);
            }

            $this->quoteRepository->save($quote);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @param $method
     * @return bool
     */
    public function filterMethod($method)
    {
        $defaultShippingMethod = $this->_checkoutHelper->getDefaultShippingMethod();
        $methodCode            = $method->getCarrierCode() . '_' . $method->getMethodCode();
        if ($methodCode == $defaultShippingMethod) {
            return true;
        }
        return false;
    }
    public function getDefaultCountryFromLocale()
    {
        $locale = $this->_objectManager->get(\Magento\Framework\Locale\Resolver::class)
            ->getLocale();

        return substr($locale, strrpos($locale, "_") + 1);
    }
}
