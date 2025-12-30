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
 
namespace Magedelight\SMSProfile\Observer\Adminhtml;

use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Event\Observer as Observer;
use Magedelight\SMSProfile\Model\SMSNotificationService;
use Magedelight\SMSProfile\Helper\Data as HelperData;
use Magedelight\SMSProfile\Api\SMSTemplatesRepositoryInterface;

class ShipmentSave implements ObserverInterface
{
     /**  @var SMSNotificationService */
    private $smsNotificationService;

    /**  @var HelperData */
    private $datahelper;

     /**  @var SMSTemplatesRepositoryInterface */
    private $smsTemplatesRepository;
    
    /**
     * Constructor
     * @param SMSNotificationService  $smsNotificationService
     * @param SMSTemplatesRepositoryInterface  $smsTemplatesRepository
     * @param HelperData $dataHelper
     */

    public function __construct(
        SMSNotificationService $smsNotificationService,
        SMSTemplatesRepositoryInterface $smsTemplatesRepository,
        HelperData $dataHelper
    ) {
        $this->smsNotificationService = $smsNotificationService;
        $this->smsTemplatesRepository  = $smsTemplatesRepository;
        $this->datahelper = $dataHelper;
    }

    /**
     * The execute class
     * @param Observer $observer
     * @return void
     */
    
    public function execute(Observer $observer)
    {
        $shipment = $observer->getEvent()->getShipment();
        $order = $shipment->getOrder();
        $customerEvent = 'customer_shipment';
        $customerEventList  = $this->datahelper->getCustomerEvents();
        if ($this->datahelper->getModuleStatus($order->getStoreId()) && (in_array($customerEvent, $customerEventList))) {
            if (is_array($this->getToNumber($order))) {
                foreach ($this->getToNumber($order) as $toNumber) {
                    if ($this->datahelper->getSendOtpVia() !='1') {
                        $this->smsNotificationService->setToNumber($toNumber);
                        $this->smsNotificationService->setMessageContent($this->getMessageText($order, $customerEvent));
                        $this->smsNotificationService->setOrderContent($this->datahelper->getOrderData($order));
                        $this->smsNotificationService->setTransactionType($customerEvent);
                        $this->smsNotificationService->setApiVersion($this->getApiVersion($order->getStoreId()));
                        $this->smsNotificationService->setCurrentStoreId($order->getStoreId());
                        $this->callSmsSending($order->getStoreId());
                    }
                }
            } else {
                if ($this->datahelper->getSendOtpVia() !='1') {
                    $this->smsNotificationService->setToNumber($this->getToNumber($order));
                    $this->smsNotificationService->setMessageContent($this->getMessageText($order, $customerEvent));
                    $this->smsNotificationService->setOrderContent($this->datahelper->getOrderData($order));
                    $this->smsNotificationService->setTransactionType($customerEvent);
                    $this->smsNotificationService->setApiVersion($this->getApiVersion($order->getStoreId()));
                    $this->smsNotificationService->setCurrentStoreId($order->getStoreId());
                    $this->callSmsSending($order->getStoreId());
                }
            }
        }
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
        return 'Shipment Created';
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

    private function callSmsSending($storeId = null)
    {
        if ($this->getApiVersion($storeId) == 'Twilio Api Service') {
            $this->smsNotificationService->sendSmsWithTwilio($storeId);
        } elseif ($this->getApiVersion($storeId) == 'BulkSms') {
            $this->smsNotificationService->sendSmsWithBulkSmsService($storeId);
        } else {
            $this->smsNotificationService->sendSmsViaOtherServices($storeId);
        }
    }
}
