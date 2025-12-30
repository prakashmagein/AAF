<?php

/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @author     Hassan <support@fmeextensions.com>
 * @package   FME_Refund
 * @copyright Copyright (c) 2021 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
    
namespace FME\Refund\Helper;
 
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use FME\Refund\Helper\Data;

 
class Email extends AbstractHelper
{
    protected $inlineTranslation;
    protected $escaper;
    protected $transportBuilder;
    protected $logger;
    protected $scopeConfig;
    protected $storeManager;
    protected $admin_name;
    protected $admin_email;


    public function __construct(
        Context $context,
        StateInterface $inlineTranslation,
        Escaper $escaper,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        Data $helperData
    ) {
        parent::__construct($context);
        $this->inlineTranslation = $inlineTranslation;
        $this->escaper = $escaper;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $context->getLogger();
        $this->scopeConfig = $scopeConfig;
        $this->helperData = $helperData; 
        $this->storeManager = $storeManager;
 
    }
// You can pass whatever the arguments here according to your requirement.
public function SendAcceptEmailToCustomer($customer_name, $customerEmail, $orderId)
    {
      
        try {
            $this->inlineTranslation->suspend();
            $sender_name = $this->scopeConfig->getValue('general/store_information/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $sender_email = $this->scopeConfig->getValue('trans_email/ident_general/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $sender = [
                'name' => $this->escaper->escapeHtml($sender_name),
                'email' => $this->escaper->escapeHtml($sender_email),
            ];
            $transport = $this->transportBuilder
                ->setTemplateIdentifier('fme_refund_email_accept_templates')
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $this->storeManager->getStore()->getId(),
                    ]
                )
                ->setTemplateVars([
                    'customer_name'  => $customer_name,
                    'order_id' => $orderId,

                ])
                ->setFrom($sender)
                ->addTo($customerEmail)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());

        }
    }

public function SendRejectEmailToCustomer($customer_name, $customerEmail, $orderId)
{
  
    try {
        $this->inlineTranslation->suspend();
        $sender_name = $this->scopeConfig->getValue('general/store_information/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $sender_email = $this->scopeConfig->getValue('trans_email/ident_general/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $sender = [
            'name' => $this->escaper->escapeHtml($sender_name),
            'email' => $this->escaper->escapeHtml($sender_email),
        ];
        $transport = $this->transportBuilder
            ->setTemplateIdentifier('fme_refund_email_reject_templates')
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->storeManager->getStore()->getId(),
                ]
            )
            ->setTemplateVars([
                'customer_name'  => $customer_name,
                'order_id' => $orderId,

            ])
            ->setFrom($sender)
            ->addTo($customerEmail)
            ->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();
    } catch (\Exception $e) {
        $this->logger->debug($e->getMessage());
    }
}
public function SendNotificationEmailToCustomer($customer_name, $customerEmail, $orderId)
{
    try {
        $this->inlineTranslation->suspend();
        $sender_name = $this->scopeConfig->getValue('general/store_information/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $sender_email = $this->scopeConfig->getValue('trans_email/ident_general/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $sender = [
            'name' => $this->escaper->escapeHtml($sender_name),
            'email' => $this->escaper->escapeHtml($sender_email),
        ];
        $transport = $this->transportBuilder
            ->setTemplateIdentifier('fme_refund_email_notification_customer_templates')
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->storeManager->getStore()->getId(),
                ]
            )
            ->setTemplateVars([
                'customer_name'  => $customer_name,
                'order_id' => $orderId,

            ])
            ->setFrom($sender)
            ->addTo($customerEmail)
            ->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();
    } catch (\Exception $e) {
        $this->logger->debug($e->getMessage());
    }
}

public function SendNotificationEmailToAdmin($customer_name, $customerEmail, $orderId, $refundReason, $refundDescription)
{

    try {
        $this->inlineTranslation->suspend();
        $sender_name = $this->scopeConfig->getValue('general/store_information/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $sender_email = $this->scopeConfig->getValue('trans_email/ident_general/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->admin_email = $this->helperData->getGeneralConfig("adminEmail");

        $sender = [
            'name' => $this->escaper->escapeHtml($sender_name),
            'email' => $this->escaper->escapeHtml($sender_email),
        ];
        
        $transport = $this->transportBuilder
            ->setTemplateIdentifier('fme_refund_email_notification_admin_templates')
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->storeManager->getStore()->getId(),
                ]
            )
            ->setTemplateVars([
                'customer_name'  => $customer_name,
                'customer_email' => $customerEmail,
                'order_id' => $orderId,
                'refund_reason' => $refundReason,
                'refund_description' => $refundDescription,
            ])

            ->setFrom($sender)
            ->addTo($this->helperData->getGeneralConfig("adminEmail"))
            ->getTransport();

        $transport->sendMessage();
        $this->inlineTranslation->resume();
        
    } catch (\Exception $e) {
        $this->logger->debug($e->getMessage());
        echo "In Catch Block ".$e;
        exit;
    }
}

}
