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

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magedelight\SMSProfile\Model\SMSNotificationService;
use Magedelight\SMSProfile\Helper\Data as HelperData;
use Magedelight\SMSProfile\Api\SMSTemplatesRepositoryInterface;

class SmsOrderAfterSave
{
    /**  @var OrderRepositoryInterface */
    private $orderRepositoryInterface;

    /**  @var SMSNotificationService */
    private $smsNotificationService;

    /**  @var HelperData */
    private $datahelper;

    /**  @var SMSTemplatesRepositoryInterface */
    private $smsTemplatesRepository;

    /**
     * Constructor
     * @param SmsNotificationService  $smsNotificationService
     * @param HelperData $dataHelper
     * @param SMSTemplatesRepositoryInterface  $smsTemplatesRepository
     * @param OrderRepositoryInterface $orderRepositoryInterface
     */
    
    public function __construct(
        SMSNotificationService $smsNotificationService,
        HelperData $dataHelper,
        SMSTemplatesRepositoryInterface $smsTemplatesRepository,
        OrderRepositoryInterface $orderRepositoryInterface
    ) {
        $this->smsNotificationService = $smsNotificationService;
        $this->datahelper = $dataHelper;
        $this->smsTemplatesRepository  = $smsTemplatesRepository;
        $this->orderRepositoryInterface = $orderRepositoryInterface;
    }

    /* @codingStandardsIgnoreStart */
    public function afterSave(\Magento\Sales\Api\OrderRepositoryInterface $orderRepo, $order)
    {
        /* @codingStandardsIgnoreEnd */
        $customerEvent = 'customer_neworder';
        $adminEvent = 'admin_new_order';
        $customerEventList  = $this->datahelper->getCustomerEvents();
        $adminEventList  = $this->datahelper->getAdminEvents();

        if ($this->datahelper->getModuleStatus($order->getStoreId()) && ($order->getState() == 'new' && $order->getStatus() == 'pending') && $order->getShippingAddress()) {
            if (in_array($customerEvent, $customerEventList)) {
                $this->sendOrderSmsToCustomer($order, $customerEvent);
            }
            if ($this->datahelper->getNotifyAdmin()) {
                if (in_array($adminEvent, $adminEventList)) {
                    $this->sendOrderSmsToAdmin($order, $adminEvent);
                }
            }
        }

        return $order;
    }

    /**  @return string */

    private function getToNumber($order)
    {
        // if ($this->datahelper->geSelectedCustomerNumber() == 'shipping_add_no') {
        //     return  $order->getShippingAddress()->getTelephone();
        // }

        // if ($this->datahelper->geSelectedCustomerNumber() == 'billing_add_no') {
        //     return  $order->getBillingAddress()->getTelephone();
        // }

        // if ($this->datahelper->geSelectedCustomerNumber() == 'both') {
        //     $no = [$order->getShippingAddress()->getTelephone(), $order->getBillingAddress()->getTelephone()];
        //     return $no;
        // }
        
        if ($this->datahelper->geSelectedCustomerNumber() == 'shipping_add_no') {
            if ($this->datahelper->isCustomerCountryEnabled()) {
                return "+". $order->getShippingAddress()->getTelephone();
            }
            return $order->getShippingAddress()->getTelephone();
        }

        if ($this->datahelper->geSelectedCustomerNumber() == 'billing_add_no') {
            if ($this->datahelper->isCustomerCountryEnabled()) {
                return "+". $order->getBillingAddress()->getTelephone();
            }
            return $order->getBillingAddress()->getTelephone();
        }

        if ($this->datahelper->geSelectedCustomerNumber() == 'both') {
            $no = [$order->getShippingAddress()->getTelephone(), $order->getBillingAddress()->getTelephone()];
            if ($this->datahelper->isCustomerCountryEnabled()) {
                $no = "+".$no;
            }
            return $no;
        }
    }

    /**  @return string */

    private function getApiVersion($storeId = null)
    {
        return  $this->datahelper->getSmsProfileApiGateWay($storeId);
    }

    /**  @return string */

    private function getTransactionType()
    {
        return 'Order Suceess';
    }

    /**  @return string */

    private function getMessageText($order, $eventType)
    {
        $sms = $this->smsTemplatesRepository->getByEventType($eventType, $order->getStoreId());
        $_message = $sms->getData('template_content');
        $message = $this->datahelper->setOrderMesageText(
            $_message,
            $this->datahelper->getOrderData($order)
        );

        return $message;
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

    public function sendOrderSmsToCustomer($order, $eventType)
    {
        if (is_array($this->getToNumber($order))) {
            $toNumber = $this->getToNumber($order);
            foreach ($toNumber as $toNumber) {
                if ($this->datahelper->getSendOtpVia() !='1') {
                    $this->smsNotificationService->setToNumber($toNumber);
                    $this->smsNotificationService->setMessageContent($this->getMessageText($order, $eventType));
                    $this->smsNotificationService->setOrderContent($this->datahelper->getOrderData($order));
                    $this->smsNotificationService->setTransactionType($eventType);
                    $this->smsNotificationService->setApiVersion($this->getApiVersion($order->getStoreId()));
                    $this->smsNotificationService->setCurrentStoreId($order->getStoreId());
                    $this->callSmsSending($order->getStoreId());
                }
            }
        } else {
            if ($this->datahelper->getSendOtpVia() !='1') {
                $this->smsNotificationService->setToNumber($this->getToNumber($order));
                $this->smsNotificationService->setMessageContent($this->getMessageText($order, $eventType));
                $this->smsNotificationService->setOrderContent($this->datahelper->getOrderData($order));
                $this->smsNotificationService->setTransactionType($eventType);
                $this->smsNotificationService->setApiVersion($this->getApiVersion($order->getStoreId()));
                $this->smsNotificationService->setCurrentStoreId($order->getStoreId());
                $this->callSmsSending($order->getStoreId());
            }
        }
    }

    public function sendOrderSmsToAdmin($order, $eventType)
    {
        $toNumber = $this->datahelper->getAdminContactNumbers();
        $_toNumber = explode(',', $toNumber);
        foreach ($_toNumber as $toNumber) {
            if ($this->datahelper->getSendOtpVia() !='1') {
                $this->smsNotificationService->setToNumber($toNumber);
                $this->smsNotificationService->setMessageContent($this->getMessageText($order, $eventType));
                $this->smsNotificationService->setTransactionType($eventType);
                $this->smsNotificationService->setApiVersion($this->getApiVersion($order->getStoreId()));
                $this->smsNotificationService->setCurrentStoreId($order->getStoreId());
                $this->callSmsSending($order->getStoreId());
            }
        }
    }
}
