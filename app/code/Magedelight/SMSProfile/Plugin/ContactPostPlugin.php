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
use Magedelight\SMSProfile\Api\SMSTemplatesRepositoryInterface;
use Magedelight\SMSProfile\Helper\Data as HelperData;

class ContactPostPlugin
{

    /**  @var SMSNotificationService */
    private $smsNotificationService;

    /**  @var HelperData */
    private $datahelper;

    /**  @var SMSTemplatesRepositoryInterface */
    private $smsTemplatesRepository;

    /**
     * Constructor
     * @param SMSNotificationService $smsNotificationService
     * @param SMSTemplatesRepositoryInterface $smsTemplatesRepository
     * @param HelperData $dataHelper
     */

    public function __construct(
        SMSNotificationService $smsNotificationService,
        SMSTemplatesRepositoryInterface $smsTemplatesRepository,
        HelperData $dataHelper
    ) {
        $this->smsNotificationService = $smsNotificationService;
        $this->datahelper = $dataHelper;
        $this->smsTemplatesRepository  = $smsTemplatesRepository;
    }
    public function afterExecute(\Magento\Contact\Controller\Index\Post $subject, $result)
    {
        $data =  $subject->getRequest()->getPostValue();
        $customerEvent = 'customer_contact';
        $adminEvent = 'admin_customer_contact';
        $customerEventList  = $this->datahelper->getCustomerEvents();
        $adminEventList  = $this->datahelper->getAdminEvents();
        if ($this->datahelper->getModuleStatus($this->datahelper->getCurrentStoreId())) {
            if (in_array($customerEvent, $customerEventList)) {
                $this->sendContactSmsToCustomer($data, $customerEvent);
            }
            if ($this->datahelper->getNotifyAdmin()) {
                if (in_array($adminEvent, $adminEventList)) {
                    $this->sendContactSmsToAdmin($data, $adminEvent);
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
        return 'Contact Us Sms';
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

    private function setContactMesageText($message, $data)
    {
        $keywords   = [
            '{name}',
            '{comment}'
        ];
        $message = str_replace($keywords, $data, $message);
        return $message;
    }

    private function sendContactSmsToCustomer($data, $eventType)
    {
        $toNumber = $data['telephone'];
        if (isset($data['countrycode'])) {
            $toNumber = $data['countrycode'].$data['telephone'];
        }
        $_data = [
            'name' => $data['name'],
            'comment'=>$data['comment'],
        ];
        $sms = $this->smsTemplatesRepository->getByEventType($eventType, $this->datahelper->getCurrentStoreId());
        $_message = $sms->getData('template_content');
        $message =$this->setContactMesageText($_message, $_data);
        if ($this->datahelper->getSendOtpVia() !='1') {
            $this->smsNotificationService->setToNumber($toNumber);
            $this->smsNotificationService->setMessageContent($message);
            $this->smsNotificationService->setTransactionType($eventType);
            $this->smsNotificationService->setApiVersion($this->getApiVersion($this->datahelper->getCurrentStoreId()));
            $this->smsNotificationService->setCurrentStoreId($this->datahelper->getCurrentStoreId());
            $this->callSmsSending($this->datahelper->getCurrentStoreId());
        }
    }

    private function sendContactSmsToAdmin($data, $eventType)
    {
        $toNumber = $this->datahelper->getAdminContactNumbers();
        $_data = [
            'name' => $data['name'],
            'comment'=>$data['comment'],
        ];
        $sms = $this->smsTemplatesRepository->getByEventType($eventType, $this->datahelper->getCurrentStoreId());
        $_message = $sms->getData('template_content');
        $message =$this->setContactMesageText($_message, $_data);
        $_toNumber = explode(',', $toNumber);
        foreach ($_toNumber as $toNumber) {
            if ($this->datahelper->getSendOtpVia() !='1') {
                $this->smsNotificationService->setToNumber($toNumber);
                $this->smsNotificationService->setMessageContent($message);
                $this->smsNotificationService->setTransactionType($eventType);
                $this->smsNotificationService->setApiVersion($this->getApiVersion($this->datahelper->getCurrentStoreId()));
                $this->smsNotificationService->setCurrentStoreId($this->datahelper->getCurrentStoreId());
                $this->callSmsSending($this->datahelper->getCurrentStoreId());
            }
        }
    }
}
