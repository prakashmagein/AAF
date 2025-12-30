<?php

namespace Gwl\Datalayerevents\Block;

use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

class Logout extends Template
{
    protected $customerSession;
    protected $checkoutSession;
    protected $sessionManager;
    protected $customData;
    protected $customerRepository;

    public function __construct(
        Template\Context $context,
        CustomerSession $customerSession,
        CheckoutSession $checkoutSession,
        SessionManagerInterface $sessionManager,
        CustomerRepositoryInterface $customerRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->sessionManager = $sessionManager;
    }

    // Function for the Logout customers
    public function getDataLayerScriptLogout()
    {
        // var_dump($this->sessionManager->getData('google_analytics_data_layer'));
        // die("lllllllll");
        //return $this->sessionManager->getData('google_analytics_data_layer');
        //var_dump($this->checkoutSession->getData());
        // var_dump($this->sessionManager->getDataLayerScriptLogout());
        // die("dddd");
        return $this->customerSession->getDataLayerScriptLogout();
    }

    public function clearDataLayerLogout()
    {
        $this->customerSession->unsDataLayerScriptLogout();
    }


    public function setCustomData($customerLogoutData)
    {
        $this->customData = $customerLogoutData;
    }

    public function getCustomData()
    {
        return $this->customData;
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

}
