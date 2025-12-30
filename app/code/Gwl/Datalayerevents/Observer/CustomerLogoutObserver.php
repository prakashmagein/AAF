<?php
namespace Gwl\Datalayerevents\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Psr\Log\LoggerInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\View\Page\Config as PageConfig;

class CustomerLogoutObserver implements ObserverInterface
{
    protected $customerSession;

    protected $logger;

    protected $sessionManager;

    protected $checkoutSession;

    protected $layout;

    protected $jsonEncoder;
    protected $pageConfig;


    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        LoggerInterface $logger,
        SessionManagerInterface $sessionManager,
        CheckoutSession $checkoutSession,
        LayoutInterface $layout,
        EncoderInterface $jsonEncoder,
        PageConfig $pageConfig
    ) {
        $this->customerSession = $customerSession;
        $this->logger = $logger;
        $this->sessionManager = $sessionManager;
        $this->checkoutSession = $checkoutSession;
        $this->layout = $layout;
        $this->jsonEncoder = $jsonEncoder;
        $this->pageConfig = $pageConfig;
    }

    public function execute(Observer $observer)
    {
        $customerLogoutData = [];
        $customer = $this->customerSession->getCustomer();
        $customerdata = $customer->getData();

        //$customer = $observer->getEvent()->getCustomer();
        $this->logger->info('Logout customer ID ' . $customer->getId());

        $customerLogoutData = [
            'event' => 'logout',
            'event_source' => 'Website',
            'user' => [
                'user_id' => $customer->getId(),
                'firstname' => $customer->getFirstname(),
                'lastname' => $customer->getLastname(),
                'email' => $customer->getEmail(),
                'phone' => $customer->getCustomerMobile(),
                'gender' => ($customer->getGender() !== NULL) ? ($customer->getGender() == 1 ? 'm' : 'f'):'N/A',
                'login_method' => 'phone',
                //'loyalty_tier' => $customer->getCustomAttribute('loyalty_tier') ? $customer->getCustomAttribute('loyalty_tier')->getValue() : 'Gold',
                'login_status' => $this->customerSession->isLoggedIn() ? 'logged_in' : 'logged_out',
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
        ];

        $this->logger->info('Logout customer Data ' . json_encode($customerLogoutData));

        // if ($this->sessionManager->getDataLayerScriptLogout()) {
        //     $this->sessionManager->unsDataLayerScriptLogout();
        // }
        //  $this->sessionManager->setDataLayerScriptLogout($customerLogoutData);


         // var_dump($this->customerSession->getDataLayerScriptLogout());
         // die("dsdsdd");
        //$this->sessionManager->setData('google_analytics_data_layer', $customerLogoutData);

         // $this->sessionManager->setData('google_analytics_data_layer', $customerLogoutData);



        // $block = $this->layout->getBlock('customer.logout.success');
        // if ($block) {
        //     $block->setCustomData($customerLogoutData);
        // }


        // $jsonData = $this->jsonEncoder->encode($customerLogoutData);

        //     $script = '<script>window.dataLayer = window.dataLayer || []; window.dataLayer.push(' . $jsonData . ');</script>';
        //     $this->pageConfig->addRemotePageAsset(
        //         $script,
        //         'script',
        //         ['attributes' => ['type' => 'text/javascript']]
        //     );


    }
}
