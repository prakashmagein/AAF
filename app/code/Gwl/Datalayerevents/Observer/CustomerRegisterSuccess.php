<?php
namespace Gwl\Datalayerevents\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\StoreManagerInterface;


class CustomerRegisterSuccess implements ObserverInterface
{
    protected $customerSession;

    protected $logger;

    protected $storeManager;


    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $this->logger->info('New customer registered: ' . $customer->getId());
        $this->customerSession->setDataLayerScript(

            [
            'event' => 'signup',
            'event_source' => $this->getWebsiteName(),
            'user' => [
                'user_id' => $customer->getId(),
                'firstname' => $customer->getFirstname(),
                'lastname' => $customer->getLastname(),
                'email' => $customer->getEmail(),
                'phone' => $customer->getCustomAttribute('customer_mobile') ? $customer->getCustomAttribute('customer_mobile')->getValue() : '',
                'gender' => ($customer->getGender() !== NULL) ? ($customer->getGender() == 1 ? 'm' : 'f'):'N/A',
                'signup_method' => 'phone',
                'loyalty_tier' => $customer->getCustomAttribute('loyalty_tier') ? $customer->getCustomAttribute('loyalty_tier')->getValue() : 'silver',
                'login_status' => $this->customerSession->isLoggedIn() ? 'logged_in' : 'sign_up',
                'berthday' => $customer->getDob(),
            ],
            'location' => [
                'country' => $customer->getCustomAttribute('country_id') ? $customer->getCustomAttribute('country_id')->getValue() : 'N/A',
                'city' => $customer->getCustomAttribute('city') ? $customer->getCustomAttribute('city')->getValue() : 'N/A',
                'zip' => $customer->getCustomAttribute('zip') ? $customer->getCustomAttribute('zip')->getValue() : 'N/A',
            ],
            'page_context' => [
                'page_type' => 'login',
            ]
        ]

        );
    }

    public function getWebsiteName()
    {
        return $this->storeManager->getWebsite()->getName();
    }
}
