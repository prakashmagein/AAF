<?php
/**
 * Magedelight
 * Copyright (C) 2022 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_SMSProfile
 * @copyright Copyright (c) 2022 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\SMSProfile\Plugin;

use Magedelight\SMSProfile\Model\SMSNotificationService;
use Magedelight\SMSProfile\Helper\Data as HelperData;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Url;
use Magedelight\SMSProfile\Api\SMSTemplatesRepositoryInterface;

class CreatePostPlugin
{

    /**  @var SMSNotificationService */
    private $smsNotificationService;

    /**  @var HelperData */
    private $datahelper;

       /**  @var Url */
    private $customerUrl;

    /**  @var StoreManagerInterface */
    private $storeManager;

    /**  @var SMSTemplatesRepositoryInterface */
    private $smsTemplatesRepository;

    /**  @var \Magento\Customer\Model\Session */
    private $customerSession;

    /**
     * Constructor
     * @param SMSNotificationService $smsNotificationService
     * @param Url $customerUrl
     * @param SMSTemplatesRepositoryInterface  $smsTemplatesRepository
     * @param StoreManagerInterface $storeManager
     * @param HelperData $dataHelper
     */

    public function __construct(
        SMSNotificationService $smsNotificationService,
        SMSTemplatesRepositoryInterface $smsTemplatesRepository,
        Url $customerUrl,
        StoreManagerInterface $storeManager,
        HelperData $dataHelper,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->smsNotificationService = $smsNotificationService;
        $this->customerUrl = $customerUrl;
        $this->datahelper = $dataHelper;
        $this->storeManager = $storeManager;
        $this->smsTemplatesRepository  = $smsTemplatesRepository;
        $this->customerSession = $customerSession;
    }

    public function afterExecute(\Magento\Customer\Controller\Account\CreatePost $subject, $result)
    {
        if ($this->customerSession->getCustomer()->getId()) {
            $data =  $subject->getRequest()->getPostValue();
            $adminEvent = 'admin_new_customer';
            $adminEventList  = $this->datahelper->getAdminEvents();
            if ($this->datahelper->getModuleStatus($this->datahelper->getCurrentStoreId())) {
                if ($this->datahelper->getNotifyAdmin()) {
                    if (in_array($adminEvent, $adminEventList)) {
                        $this->sendSmsToAdmin($data, $adminEvent);
                    }
                }
            }
        }
         
        return $result;
    }

    private function getApiVersion($storeId = null)
    {
        return  $this->datahelper->getSmsProfileApiGateWay($storeId);
    }
    
    /**  @return string */

    private function getTransactionType()
    {
        return 'Customer Registeration success';
    }

    public function callSmsSending($storeId = null)
    {
        if ($this->getApiVersion($storeId) == 'Twilio Api Service') {
            $this->smsNotificationService->sendSmsWithTwilio($storeId);
        } elseif ($this->getApiVersion($storeId) == 'BulkSms') {
            $this->smsNotificationService->sendSmsWithBulkSmsService($storeId);
        } else {
            $this->smsNotificationService->sendSmsViaOtherServices($storeId);
        }
    }

    private function setWelcomeMesageText($message, $data)
    {
        $keywords   = [
            '{name}',
            '{store}',
            '{username}',
            '{url}',
        ];
        $message = str_replace($keywords, $data, $message);
        return $message;
    }

    public function getCustomerLoginUrl()
    {
        return $this->customerUrl->getLoginUrl();
    }

    public function getCurrentStoreName()
    {
        return $this->storeManager->getStore()->getName();
    }

    public function sendSmsToAdmin($data, $eventType)
    {
        $toNumber = $this->datahelper->getAdminContactNumbers();
        $_data = $this->getCustomerData($data);
        $sms = $this->smsTemplatesRepository->getByEventType($eventType, $this->datahelper->getCurrentStoreId());
        $_message = $sms->getData('template_content');
        $message =$this->setWelcomeMesageText($_message, $_data);

        $_toNumber = explode(',', $toNumber);
        foreach ($_toNumber as $toNumber) {
            if ($this->datahelper->getSendOtpVia() !='1') {
                $this->smsNotificationService->setToNumber($toNumber);
                $this->smsNotificationService->setMessageContent($message);
                $this->smsNotificationService->setTransactionType($eventType);
                $this->smsNotificationService->setApiVersion($this->getApiVersion($this->storeManager->getStore()->getStoreId()));
                $this->smsNotificationService->setCurrentStoreId($this->storeManager->getStore()->getStoreId());
                $this->callSmsSending($this->storeManager->getStore()->getStoreId());
            }
        }
    }

    public function getCustomerData($data)
    {
        $_data = [
            'name' => $data['firstname'].' '.$data['lastname'],
            'store'=> $this->getCurrentStoreName(),
            'username' =>$data['email'],
            'url' =>  $this->getCustomerLoginUrl(),
        ];

        return $_data;
    }
}
