<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magedelight\SMSProfile\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

use Magedelight\SMSProfile\Model\SMSProfileTemplatesFactory;
use Magedelight\SMSProfile\Model\SMSTemplatesFactory;

/**
 * Patch is mechanism, that allows to do atomic upgrade data changes
 */
class AddSmsProfileTemplates implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

     /**
      * @var SMSProfileTemplatesFactory
      */
    private $smsProfileTemplates;

    /**
     * @var SMSTemplatesFactory
     */
    private $smsTemplates;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        SMSProfileTemplatesFactory $smsProfileTemplates,
        SMSTemplatesFactory $smsTemplates
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->smsProfileTemplates = $smsProfileTemplates;
        $this->smsTemplates = $smsTemplates;
    }

    /**
     * Do Upgrade
     *
     * @return void
     */
    public function apply()
    {
        /** Add Templates in magedelight_smstemplates Table  Start */
        $this->AddSmsTemplates();
        /** Add Templates in magedelight_smstemplates Table  End */

          /** Add Templates in magedelight_smstemplates Table  Start */
        $this->addNotiSmsTemplates();
        /** Add Templates in magedelight_smstemplates Table  End */
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    public function AddSmsTemplates()
    {
        $smsProfileTemplate = [];

        $smsProfileTemplate[0] = [
           'template_name' =>'Customer Signup otp sms For All Store View',
           'template_content' => 'Your otp for signup is {otpCode} .Please do not share it with others.',
           'event_type' =>'customer_signup_otp',
           'store_id' =>0,
        ];

        $smsProfileTemplate[1] = [
           'template_name' =>'Customer login otp sms For All Store View',
           'template_content' => 'Your otp for login is {otpCode} .Please do not share it with others.',
           'event_type' =>'customer_login_otp',
           'store_id' =>0,
        ];

        $smsProfileTemplate[2] = [
           'template_name' =>'Customer account update otp sms For All Store View',
           'template_content' => 'Your otp for update account is {otpCode} .Please do not share it with others.',
           'event_type' =>'customer_account_edit_otp',
           'store_id' =>0,
        ];
        
        $smsProfileTemplate[3] = [
           'template_name' =>'OTP For Cod Payment For All Store View',
           'template_content' => 'Your otp for cod payment  is {otpCode} .Please do not share it with others.',
           'event_type' =>'cod_otp',
           'store_id' =>0,
        ];

        $smsProfileTemplate[4] = [
           'template_name' =>'OTP For Cod Payment For All Store View',
           'template_content' => 'Your otp for cod payment  is {otpCode} .Please do not share it with others.',
           'event_type' =>'cod_otp',
           'store_id' =>0,
        ];

        $smsProfileTemplate[4] = [
           'template_name' =>'OTP For Forgot Password With All Store View',
           'template_content' => 'Your otp for forgot password is {otpCode} .Please do not share it with others.',
           'event_type' =>'forgot_password_otp',
           'store_id' =>0,
        ];

        /**
         * Insert default crosslinks
         */
        foreach ($smsProfileTemplate as $data) {
            $this->createSmsProfileTemplates()->setData($data)->save();
        }
    }

    /**
     * Create smsTemplate
     *
     * @return smsTemplate
     */
    public function createSmsProfileTemplates()
    {
        return $this->smsProfileTemplates->create();
    }

    public function addNotiSmsTemplates()
    {
        $smsTemplate = [];
        $smsTemplate[0] = [
           'template_name' =>'Customer New Order Templates For All Store View',
            'template_content' => 'Dear {firstname} {lastname}, Thank you for your order.You have placed order with id {order_id} and amounting {total}. Your order items are {orderitem} . You will receive your order within 7-8 business working day.',
           'event_type' =>'customer_neworder',
           'store_id' =>0,
        ];

        $smsTemplate[1] = [
           'template_name' =>'Customer Invoice Proceed Template For All Store View',
           'template_content' => 'Dear {firstname} {lastname}, Thank you for your payment. We have received payment of your order {order_id} and amount is {total}. Your order items are {orderitem} .',
           'event_type' =>'customer_invoice',
           'store_id' =>0,
        ];

        $smsTemplate[2] = [
           'template_name' =>'Customer Creditmemo  Processed Template For All Store View',
           'template_content' => 'Dear {firstname} {lastname},Your refund request for  Order {order_id} has been accepted.',
           'event_type' =>'customer_creditmemo',
           'store_id' =>0,
        ];

        $smsTemplate[3] = [
           'template_name' =>'Customer Shipment Create Template For All Store View',
           'template_content' => 'Dear {firstname} {lastname},Your Order {order_id} has been shipped. you will receive order by today or tomorrow. Your oreder items are {orderitem} .',
           'event_type' =>'customer_shipment',
           'store_id' =>0,
        ];

        $smsTemplate[4] = [
           'template_name' =>'Customer Order Cancel Template For All Store View',
           'template_content' => 'Dear {firstname} {lastname},Your Order {order_id} has been cancelled due to item is not available.',
           'event_type' =>'customer_order_cancel',
           'store_id' =>0,
        ];

        $smsTemplate[5] = [
           'template_name' =>'Customer Contact Template For All Store View',
           'template_content' => 'Dear {name} ,Thank you for contacting us. You have Contact us for "{comment}". we will respond you soon.',
           'event_type' =>'customer_contact',
           'store_id' =>0,
        ];

        $smsTemplate[6] = [
           'template_name' =>'Admin Neworder Template For All Store View',
           'template_content' => 'Dear Admin,New Order {order_id} with amount of {total} placed in your {store}',
           'event_type' =>'admin_new_order',
           'store_id' =>0,
        ];

        $smsTemplate[7] = [
           'template_name' =>'Admin New Customer Register Template For All Store View',
           'template_content' => 'Dear Admin,New Customer is registered in your {store}',
           'event_type' =>'admin_new_customer',
           'store_id' =>0,
        ];

        $smsTemplate[8] = [
           'template_name' =>'Admin Customer Contact Template For All Store View',
           'template_content' => 'Dear Admin, Customer {name}  has contacted with  {comment}.  ',
           'event_type' =>'admin_customer_contact',
           'store_id' =>0,
        ];

        $smsTemplate[9] = [
           'template_name' =>'Shipment Tracking For All Store View',
           'template_content' => 'Dear, we have shipped your Order {order_id}. we have shipped your order from {trackingtitle}.your tracking number is {tracknumber}. ',
           'event_type' =>'customer_shipment_tracking',
           'store_id' =>0,
        ];

        /**
         * Insert default crosslinks
         */
        foreach ($smsTemplate as $data) {
            $this->createSmsTemplates()->setData($data)->save();
        }
    }

    /**
     * Create smsTemplate
     *
     * @return smsTemplate
     */
    public function createSmsTemplates()
    {
        return $this->smsTemplates->create();
    }
}
