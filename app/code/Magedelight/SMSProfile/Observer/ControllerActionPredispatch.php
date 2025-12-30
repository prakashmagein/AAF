<?php

namespace Magedelight\SMSProfile\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magedelight\SMSProfile\Helper\Data as HelperData;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\App\Response\RedirectInterface;
use \Magento\Framework\Message\ManagerInterface;

class ControllerActionPredispatch implements ObserverInterface
{
    
    /**  @var HelperData */
    private $datahelper;
    /**
     * @var Session
     */
    protected $customerSession;

    /** @var CustomerRepositoryInterface */
    private $customerCollection;

    /** @var RedirectInterface */
    private $redirect;

     /** @var ManagerInterface */
    private $messageManager;

    private $request;

    public function __construct(
        HelperData $dataHelper,
        Session $customerSession,
        CollectionFactory $customerCollection,
        RedirectInterface $redirect,
        ManagerInterface $messageManager,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->datahelper = $dataHelper;
        $this->customerSession = $customerSession;
        $this->customerCollection = $customerCollection;
        $this->redirect = $redirect;
        $this->messageManager = $messageManager;
        $this->request = $request;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $actionArray = ['smsprofile_otp_send','customer_account_edit','smsprofile_otp_verify'];
        $currentAction = $this->request->getFullActionName();
        if ($this->datahelper->getModuleStatus() && $this->customerSession->isLoggedIn() && $this->datahelper->redirectOldCustomer() && !in_array($currentAction, $actionArray)) {
            $customerId = $this->customerSession->getId();
            if ($this->getCurrentCustomerPhone($customerId) === null) {
                $this->messageManager->addNoticeMessage(__('Please enter mobile number.'));
                $controller = $observer->getControllerAction();
                $this->redirect->redirect($controller->getResponse(), 'customer/account/edit');
            }
        }
        return $this;
    }

    private function getCurrentCustomerPhone($id)
    {
        $tel = '';
        $customerCollection = $this->customerCollection->create();
        $customerCollection->addAttributeToSelect('*')
                           ->addFieldToFilter('entity_id', $id)
                           ->load();
        foreach ($customerCollection as $_customer) {
            $tel = $_customer->getCustomerMobile();
        }
        if ($tel != '') {
            return $tel;
        }
        return null;
    }
}
