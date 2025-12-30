<?php

namespace Gwl\Datalayerevents\Block;

use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;


class DataLayer extends Template
{
    protected $customerSession;
    protected $checkoutSession;
    protected $storeManager;
    protected $localeResolver;
    protected $customerRepository;


    public function __construct(
        Template\Context $context,
        CustomerSession $customerSession,
        CheckoutSession $checkoutSession,
        StoreManagerInterface $storeManager,
        ResolverInterface $localeResolver,
        CustomerRepositoryInterface $customerRepository,
        array $data = []
    ) {
        
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->storeManager = $storeManager;
        $this->localeResolver = $localeResolver;
        $this->customerRepository = $customerRepository;
        parent::__construct($context, $data);
    }

    // Function for the register customers
    public function getDataLayerScript()
    {
        return $this->customerSession->getDataLayerScript();
    }

    public function clearDataLayer()
    {
        $this->customerSession->unsDataLayerScript();
    }


    // Function for the login customers
    public function getDataLayerScriptLogin()
    {
        return $this->customerSession->getDataLayerScriptLogin();
    }

    public function clearDataLayerLogin()
    {
        $this->customerSession->unsDataLayerScriptLogin();
    }


    // Function for the Logout customers
    public function getDataLayerScriptLogout()
    {
        return $this->customerSession->getDataLayerScriptLogout();
    }

    public function clearDataLayerLogout()
    {
        $this->customerSession->unsDataLayerScriptLogout();
    }


    // Function for the add to cart
    public function getDataLayerScriptAddtocart()
    {
        return $this->customerSession->getDataLayerScriptAddtocart();
    }

    public function clearDataLayerAddtocart()
    {
        $this->customerSession->unsDataLayerScriptAddtocart();
    }

    // Function for the add to cart
    public function getDataLayerScriptWishlist()
    {
        return $this->customerSession->getProductDataWishlist();
    }

    public function clearDataLayerWishlist()
    {
        $this->customerSession->unsProductDataWishlist();
    }


    // Function for the remove cart item
    public function getDataLayerScriptRemoveItem()
    {
        return $this->checkoutSession->getRemoveItem();
    }

    public function clearDataLayerRemoveItem()
    {
        $this->checkoutSession->getRemoveItem();
    }

    // For the customer global variable
    public function getCustomerSession()
    {
        return $this->customerSession;
    }

    public function getWebsiteName()
    {
        return $this->storeManager->getWebsite()->getName();
    }

    public function getCurrentLocale()
    {
         return $this->localeResolver->getLocale();
    }

    public function getStoreName()
    {
        return $this->storeManager->getStore()->getName();
    }

    public function getCustomerInformation()
    {
    
        if ($this->customerSession->isLoggedIn()) {
            $customerId = $this->customerSession->getCustomerId();
            try {
                $customer = $this->customerRepository->getById($customerId);
                return $customer;
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }


    public function getCustomerAddress()
    {
        $customer = $this->getCustomerInformation();
        if ($customer) {
            $addresses = $customer->getAddresses();
            if (!empty($addresses)) {
                $defaultBillingId = $customer->getDefaultBilling();
                $defaultShippingId = $customer->getDefaultShipping();
                
                foreach ($addresses as $address) {
                    if ($address->getId() == $defaultBillingId || $address->getId() == $defaultShippingId) {
                        return [
                            'city' => $address->getCity(),
                            'postcode' => $address->getPostcode(),
                            'country' => $address->getCountryId(),
                        ];
                    }
                }
            }
        }
        return null;
    }

}