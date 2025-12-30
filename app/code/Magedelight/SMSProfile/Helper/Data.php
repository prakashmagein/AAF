<?php
/**
 * Magedelight
 * Copyright (C) 2022 Magedelight <info@magedelight.com>
 *
 * @category  Magedelight
 * @package   Magedelight_SMSProfile
 * @copyright Copyright (c) 2022 Mage Delight (http://www.magedelight.com/)
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author    Magedelight <info@magedelight.com>
 */

namespace Magedelight\SMSProfile\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;

class Data extends \Magento\Framework\App\Helper\AbstractHElper
{
    const XML_PATH_ENABLE = 'magedelightsmsprofile/general/enable';
    const XML_PATH_API_GATEWAY = 'magedelightsmsprofile/general/api_gateway';
    const XML_PATH_TWILIO_ACCOUNT_SID = 'magedelightsmsprofile/general/account_sid';
    const XML_PATH_TWILIO_ACCOUNT_TOKEN = 'magedelightsmsprofile/general/auth_token';
    const XML_PATH_TWILIO_PHONE = 'magedelightsmsprofile/general/twilio_phone';
    const XML_PATH_TWILIO_SERVICE_ID = 'magedelightsmsprofile/general/twilio_service_id';
    const XML_PATH_API_URL = 'magedelightsmsprofile/general/api_url';
    const XML_PATH_API_CREDENTIAL = 'magedelightsmsprofile/general/api_credential';
    const XML_PATH_API_TO = 'magedelightsmsprofile/general/api_to';
    const XML_PATH_API_BODY = 'magedelightsmsprofile/general/api_message';
    const XML_PATH_API_COUNTRY = 'magedelightsmsprofile/general/api_country';
    const XML_PATH_API_PARAMS = 'magedelightsmsprofile/general/api_params';
    const XML_PATH_API_UPDATEPARAMS = 'magedelightsmsprofile/general/api_updateparams';
    const XML_PATH_API_URL_PROMOTIONAL = 'magedelightsmsprofile/general/api_url_promotional';
    const XML_PATH_API_FETCHURL = 'magedelightsmsprofile/general/api_url_status';
    const XML_PATH_API_PROCESS_STATUS = 'magedelightsmsprofile/general/process_status';
    const XML_PATH_API_FAIL_STATUS = 'magedelightsmsprofile/general/fail_status';
    const XML_PATH_API_ERROR = 'magedelightsmsprofile/general/error_key';
    const XML_PATH_DEFAULT_COUNTRY = 'magedelightsmsprofile/general/default_country';
    const XML_PATH_FAILURE_NOTIFICATION_ENABLE = 'magedelightsmsprofile/adminnotity/failurenotification/enable';
    const XML_PATH_NOTIFY_TO_MAIL = 'magedelightsmsprofile/adminnotity/failurenotification/notifytomail';
    const XML_PATH_NOTIFY_FROM_MAIL = 'magedelightsmsprofile/adminnotity/failurenotification/notifymailsender';
    const XML_PATH_SMSPROFILELOG_ENABLE = 'magedelightsmsprofile/alllog/smsprofilelog/enable';
    const XML_PATH_SMSPROFILELOG_CRON_ENABLE = 'magedelightsmsprofile/alllog/smsprofilelog/cron_enable';
    const XML_PATH_SMSPROFILELOG_TIMEOUT = 'magedelightsmsprofile/alllog/smsprofilelog/cron_timeout';
    const XML_PATH_PHONENUMBERREQUIRED_SIGNUP = 'magedelightsmsprofile/otpsetting/required_phone';
    const XML_PATH_PHONE_MAX = 'magedelightsmsprofile/general/phone_max';
    const XML_PATH_PHONE_MIN = 'magedelightsmsprofile/general/phone_min';
    const XML_PATH_OTP_FORMAT = 'magedelightsmsprofile/otpsetting/otp_format';
    const XML_PATH_OTP_LENGTH = 'magedelightsmsprofile/otpsetting/otp_length';
    const XML_PATH_OTP_LOGIN = 'magedelightsmsprofile/otpsetting/otp_login';
    const XML_PATH_PHONE_NOTE = 'magedelightsmsprofile/otpsetting/phone_note';
    const XML_PATH_PHONE_EAV = 'magedelightsmsprofile/otpsetting/phone_eav';
    const XML_PATH_OTP_COD = 'magedelightsmsprofile/otpsetting/otp_cod';
    const XML_PATH_OTP_EXPIRY = 'magedelightsmsprofile/otpsetting/otp_expiry';
    const XML_PATH_XML_REQUEST_RESPONSE = 'magedelightsmsprofile/general/api_xml';
    const XML_PATH_OTP_API_GET_REQUEST = 'magedelightsmsprofile/general/api_get_request';
    const XML_PATH_ENABLE_ON_CHECKOUT = 'magedelightsmsprofile/general/enable_on_checkout';

    const XML_PATH_CUSTOMER_COUNTRY = 'magedelightsmsprofile/general/customer_country';
    const XML_PATH_DEFAULT_CUSTOMER_COUNTRY = 'magedelightsmsprofile/general/default_customer_country';
    const XML_PATH_AVAILABLE_COUNTRIES = 'magedelightsmsprofile/general/available_customer_country';

    const XML_PATH_RESEND_TIME = 'magedelightsmsprofile/otpsetting/resend_time';
    const XML_PATH_SEND_OTP_VIA = 'magedelightsmsprofile/general/send_otp_via';

    /** SMS Notification */
    const XML_PATH_NOTIFYADMIN_ENABLE = 'magedelightsmsprofile/adminnotity/adminSms/notifyadmin';
    const XML_PATH_ADMIN_CONTACT = 'magedelightsmsprofile/adminnotity/adminSms/admin_no';
    const XML_PATH_ADMIN_EVENTS = 'magedelightsmsprofile/adminnotity/adminSms/admin_events';
    const XML_PATH_ADMIN_NOTIFYFAILURE = 'magedelightsmsprofile/adminnotity/adminSms/failurenotification';
    const XML_PATH_ADMIN_NOTIFYTOMAIL = 'magedelightsmsprofile/adminnotity/adminSms/notifytomail';
    const XML_PATH_ADMIN_NOTIFYFROMMAIL = 'magedelightsmsprofile/adminnotity/adminSms/notifymailsender';

    const XML_PATH_CUSTOMER_EVENTS = 'magedelightsmsprofile/customerSms/customer_events';
    const XML_PATH_SMSLOG_ENABLE = 'magedelightsmsprofile/alllog/smslog/enable';
    const XML_PATH_SMSLOG_CRON_ENABLE = 'magedelightsmsprofile/alllog/smslog/cron_enable';
    const XML_PATH_SELECT_CUSTOMER_NO = 'magedelightsmsprofile/customerSms/customer_no';
    const XML_PATH_PHONE_MAX_LENGTH = 'magedelightsmsprofile/general/phone_max';
    const XML_PATH_PHONE_MIN_LENGTH = 'magedelightsmsprofile/general/phone_min';
    const XML_PATH_PHONE_NOTICE = 'magedelightsmsprofile/general/phone_notice';
   
    const XML_PATH_BULKSMS_URL = 'magedelightsmsprofile/general/bilksmsurl';
    const XML_PATH_BULKSMS_USER = 'magedelightsmsprofile/general/bilksmsusername';
    const XML_PATH_BULKSMS_PASSWORD = 'magedelightsmsprofile/general/bilksmspassword';

    /** Auto Login Trigger  */
    const XML_PATH_AUTO_GENERATE_LOGIN = 'magedelightsmsprofile/general/auto_generate_enable';
    const XML_PATH_AUTO_VERIFY_OTP = 'magedelightsmsprofile/general/auto_generate_enable';
    const XML_PATH_MOBILE_NUMBER_LIMIT ='magedelightsmsprofile/general/phone_max';
     /**  OTP Template  */
     const XML_PATH_OTP_TEMPLATE_LOGIN = 'magedelightsmsprofile/otptemplate/customer_login_otp_template';
     const XML_PATH_OTP_TEMPLATE_SIGNUP ='magedelightsmsprofile/otptemplate/customer_signup_otp_template';
     const XML_PATH_OTP_TEMPLATE_UPDATE ='magedelightsmsprofile/otptemplate/customer_account_edit_otp_template';
     const XML_PATH_OTP_TEMPLATE_FORGOT ='magedelightsmsprofile/otptemplate/forgot_password_otp_template';
     const XML_PATH_OTP_TEMPLATE_COD ='magedelightsmsprofile/otptemplate/cod_otp_template';

    /**  OTP Resend Limit  */
    const XML_PATH_OTP_RESEND_LIMIT_ENABLE = 'magedelightsmsprofile/otpsetting/otpresend_enable';
    const XML_PATH_OTP_RESEND_LIMIT ='magedelightsmsprofile/otpsetting/otpresend_limit';
    //const XML_PATH_OTP_RESEND_TIME ='magedelightsmsprofile/otpresendlimit/otpresend_time';
     /**  Notification Template  */
     // const XML_PATH_NOTIFICATION_SENDER_ID ='magedelightsmsprofile/notificationtemplate/sender_id_notification';
     const XML_PATH_NOTIFICATION_TEMPLATE_CUSTOMER_NEWORDER = 'magedelightsmsprofile/notificationtemplate/customer_neworder_notification_template';
     const XML_PATH_NOTIFICATION_TEMPLATE_CUSTOMER_INVOICE ='magedelightsmsprofile/notificationtemplate/customer_invoice_notification_template';
     const XML_PATH_NOTIFICATION_TEMPLATE_CUSTOMER_CREDITMEMO ='magedelightsmsprofile/notificationtemplate/customer_creditmemo_notification_template';
     const XML_PATH_NOTIFICATION_TEMPLATE_CUSTOMER_SHIPMENT ='magedelightsmsprofile/notificationtemplate/customer_shipment_notification_template';
     const XML_PATH_NOTIFICATION_TEMPLATE_CUSTOMER_ORDER_CANCEL ='magedelightsmsprofile/notificationtemplate/customer_order_cancel_notification_template';
     const XML_PATH_NOTIFICATION_TEMPLATE_CUSTOMER_CONTACT = 'magedelightsmsprofile/notificationtemplate/customer_contact_notification_template';
     const XML_PATH_NOTIFICATION_TEMPLATE_ADMIN_NEW_ORDER ='magedelightsmsprofile/notificationtemplate/admin_new_order_notification_template';
     const XML_PATH_NOTIFICATION_TEMPLATE_ADMIN_NEW_CUSTOMER ='magedelightsmsprofile/notificationtemplate/admin_new_customer_notification_template';
     const XML_PATH_NOTIFICATION_TEMPLATE_ADMIN_CUSTOMER_CONTACT ='magedelightsmsprofile/notificationtemplate/admin_customer_contact_notification_template';
     const XML_PATH_NOTIFICATION_TEMPLATE_CUSTOMER_SHIPMENT_TRACKING ='magedelightsmsprofile/notificationtemplate/customer_shipment_tracking_notification_template';

      /* notification URL */
    const XML_PATH_API_URL_NOTIFICATION = 'magedelightsmsprofile/general/api_url_notification';

    const XML_PATH_AUTHORIZATION_HEADER = 'magedelightsmsprofile/general/api_authorization_header';

    const XML_PATH_EMAILREQUIRED_SIGNUP = 'magedelightsmsprofile/otpsetting/required_email';
    const XML_PATH_MOBILEVERIFICATION_SIGNUP = 'magedelightsmsprofile/otpsetting/verify_mobile';

    const XML_PATH_MOBILE_CUSTOMER_REDIRECT = 'magedelightsmsprofile/otpsetting/add_phone';
    const XML_PATH_MOBILE_LOGIN_POPUP_ENABLE = 'magedelightsmsprofile/general/enable_login_popup';

    const XML_PATH_SMS_ALERT = 'magedelightsmsprofile/email_template/smsprofilealert_template';
    const XML_PATH_SEND_OTP = 'magedelightsmsprofile/email_template/sendotp_template';
    const XML_PATH_SMS_NOTIFICATION = 'magedelightsmsprofile/email_template/notification_template';
    const XML_PATH_PHONE_VALIDATION = 'magedelightsmsprofile/otpsetting/phone_validation';

    const XML_PATH_RECAPTCHA_STATUS = 'magedelightsmsprofile/otpsetting/recaptcha_settings/enable';
    const XML_PATH_RECAPTCHA_SITEKEY = 'magedelightsmsprofile/otpsetting/recaptcha_settings/recaptcha_sitekey';
    const XML_PATH_RECAPTCHA_SECRETKEY = 'magedelightsmsprofile/otpsetting/recaptcha_settings/recaptcha_secret';
    const XML_PATH_RECAPTCHA_FORMS = 'magedelightsmsprofile/otpsetting/recaptcha_settings/recaptcha_forms';

    const XML_PATH_GUEST_CHECKOUT_LOGIN = 'checkout/options/enable_guest_checkout_login';


    /**  @var StoreManagerInterface */
    private $storeManager;

    /**  @var CurrencyInterface */
    private $localecurrency;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $_cookieManager;
    
    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $_cookieMetadataFactory;

    /**
     * @var \Magedelight\SMSProfile\Model\ResourceModel\SMSTemplates\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magedelight\SMSProfile\Model\ResourceModel\SMSProfileTemplates\CollectionFactory
     */
    protected $collectionFactoryOTPTemplate;

    protected $serialize;

    /**
     * Constructor
     * @param Context $context
     * @param CurrencyInterface $localeCurrency
     * @param StoreManagerInterface $storeManager
     */

    public function __construct(
        Context $context,
        CurrencyInterface $localeCurrency,
        StoreManagerInterface $storeManager,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        \Customization\CityData\Model\Cities $cities,
        \Magedelight\SMSProfile\Model\ResourceModel\SMSTemplates\CollectionFactory $collectionFactoryNotificationTemplate,
        \Magedelight\SMSProfile\Model\ResourceModel\SMSProfileTemplates\CollectionFactory $collectionFactoryOTPTemplate,
        \Magento\Framework\Serialize\Serializer\Json $serialize
    ) {
        $this->storeManager = $storeManager;
        $this->localecurrency = $localeCurrency;
        $this->_cookieManager = $cookieManager;
        $this->_cookieMetadataFactory = $cookieMetadataFactory;
        $this->_cities = $cities;
        $this->collectionFactory = $collectionFactoryNotificationTemplate;
        $this->collectionFactoryOTPTemplate = $collectionFactoryOTPTemplate;
        $this->serialize = $serialize;
        parent::__construct($context);
    }
    
    /** @return bool */

    public function getModuleStatus()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    // public function getSenderIdNotification()
    // {
    //     return $this->scopeConfig->getValue(
    //         self::XML_PATH_NOTIFICATION_SENDER_ID,
    //         ScopeInterface::SCOPE_STORE
    //     );
    // }
    public function getAllCity()
    {
        return $this->_cities->getAllOptions();
    }
     /**  @return string */
    public function getSmsProfileApiUrlForNotification()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_URL_NOTIFICATION,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getSenderTemplateNotificationData($dataArr)
    {
        $param = [];
        $dataArray =  explode(',', $dataArr);
        foreach ($dataArray as $dataArray) {
            $param[preg_replace('/^([^:]*).*$/', '$1', $dataArray)] = substr($dataArray, strpos($dataArray, ":") + 1);
        }
        return $param;
    }

     /**  @return string */

    public function getCustomerNewOrderNotificationTemplate($event_type, $storeId)
    {
        $notification_templates = $this->collectionFactory->create()->addFieldToFilter('event_type', $event_type);
        foreach ($notification_templates as $key => $notification_template) {
            $data = $notification_template->getNotificationTemplate();
        }
        return $this->getSenderTemplateNotificationData($data);
    }

    public function getCustomerInvoiceNotificationTemplate($event_type, $storeId)
    {
        $notification_templates = $this->collectionFactory->create()->addFieldToFilter('event_type', $event_type);
        foreach ($notification_templates as $key => $notification_template) {
            $data = $notification_template->getNotificationTemplate();
        }
        return $this->getSenderTemplateNotificationData($data);
    }

    public function getCustomerCreditmemoNotificationTemplate($event_type, $storeId)
    {
        $notification_templates = $this->collectionFactory->create()->addFieldToFilter('event_type', $event_type);
        foreach ($notification_templates as $key => $notification_template) {
            $data = $notification_template->getNotificationTemplate();
        }
        return $this->getSenderTemplateNotificationData($data);
    }

    public function getCustomerShipmentNotificationTemplate($event_type, $storeId)
    {
        $notification_templates = $this->collectionFactory->create()->addFieldToFilter('event_type', $event_type);
        foreach ($notification_templates as $key => $notification_template) {
            $data = $notification_template->getNotificationTemplate();
        }
        return $this->getSenderTemplateNotificationData($data);
    }

    public function getCustomerOrderCancelNotificationTemplate($event_type, $storeId)
    {
        $notification_templates = $this->collectionFactory->create()->addFieldToFilter('event_type', $event_type);
        foreach ($notification_templates as $key => $notification_template) {
            $data = $notification_template->getNotificationTemplate();
        }
        return $this->getSenderTemplateNotificationData($data);
    }

    public function getCustomerContactNotificationTemplate($event_type, $storeId)
    {
        $notification_templates = $this->collectionFactory->create()->addFieldToFilter('event_type', $event_type);
        foreach ($notification_templates as $key => $notification_template) {
            $data = $notification_template->getNotificationTemplate();
        }
        return $this->getSenderTemplateNotificationData($data);
    }

    public function getAdminNewOrderNotificationTemplate($event_type, $storeId)
    {
        $notification_templates = $this->collectionFactory->create()->addFieldToFilter('event_type', $event_type);
        foreach ($notification_templates as $key => $notification_template) {
            $data = $notification_template->getNotificationTemplate();
        }
        return $this->getSenderTemplateNotificationData($data);
    }

    public function getAdminNewCustomerNotificationTemplate($event_type, $storeId)
    {
        $notification_templates = $this->collectionFactory->create()->addFieldToFilter('event_type', $event_type);
        foreach ($notification_templates as $key => $notification_template) {
            $data = $notification_template->getNotificationTemplate();
        }
        return $this->getSenderTemplateNotificationData($data);
    }

    public function getAdminCustomerContactNotificationTemplate($event_type, $storeId)
    {
        $notification_templates = $this->collectionFactory->create()->addFieldToFilter('event_type', $event_type);
        foreach ($notification_templates as $key => $notification_template) {
            $data = $notification_template->getNotificationTemplate();
        }
        return $this->getSenderTemplateNotificationData($data);
    }

    public function getCustomerShipmentTrackingNotificationTemplate($event_type, $storeId)
    {
        $notification_templates = $this->collectionFactory->create()->addFieldToFilter('event_type', $event_type);
        foreach ($notification_templates as $key => $notification_template) {
            $data = $notification_template->getNotificationTemplate();
        }
        return $this->getSenderTemplateNotificationData($data);
    }

    /**  @return string */

    public function getOTPLoginTemplate($event_type, $storeId)
    {
        $notification_templates = $this->collectionFactoryOTPTemplate->create()->addFieldToFilter('event_type', $event_type);
        foreach ($notification_templates as $key => $notification_template) {
            return $notification_template->getOtpTemplate();
        }
    }

    /**  @return string */

    public function getOTPSignUpTemplate($event_type, $storeId)
    {
        $notification_templates = $this->collectionFactoryOTPTemplate->create()->addFieldToFilter('event_type', $event_type);
        foreach ($notification_templates as $key => $notification_template) {
            return $notification_template->getOtpTemplate();
        }
    }

    /**  @return string */

    public function getOTPForgotPasswordTemplate($event_type, $storeId)
    {
        $notification_templates = $this->collectionFactoryOTPTemplate->create()->addFieldToFilter('event_type', $event_type);
        foreach ($notification_templates as $key => $notification_template) {
            return $notification_template->getOtpTemplate();
        }
    }

    public function getOTPCODTemplate($event_type, $storeId)
    {
        $notification_templates = $this->collectionFactoryOTPTemplate->create()->addFieldToFilter('event_type', $event_type);
        foreach ($notification_templates as $key => $notification_template) {
            return $notification_template->getOtpTemplate();
        }
    }

    /**  @return string */

    public function getOTPAccountUpdateTemplate($event_type, $storeId)
    {
        $notification_templates = $this->collectionFactoryOTPTemplate->create()->addFieldToFilter('event_type', $event_type);
        foreach ($notification_templates as $key => $notification_template) {
            return $notification_template->getOtpTemplate();
        }
    }

    /** @return bool */

    public function getFailureNotificationStatus()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FAILURE_NOTIFICATION_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** @return bool */

    public function getSmsProfileLogStatus()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SMSPROFILELOG_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    
    /** @return bool */

    public function getAPIRequestInGet($storeId = null)
    {
        if ($storeId == null) {
            return $this->scopeConfig->getValue(
                self::XML_PATH_OTP_API_GET_REQUEST,
                ScopeInterface::SCOPE_STORE
            );
        }
        return $this->scopeConfig->getValue(
            self::XML_PATH_OTP_API_GET_REQUEST,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }


    /** @return bool */

    public function getApiReauestResponseXML()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_XML_REQUEST_RESPONSE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** @return bool */

    public function getSmsProfileCronStatus()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SMSPROFILELOG_CRON_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** @return bool */

    public function getSmsProfilePhoneRequiredOnSignUp()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHONENUMBERREQUIRED_SIGNUP,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** @return bool */

    public function getOtpForCOD()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_OTP_COD,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** @return string */

    public function getSmsProfileOtpOnLogin()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_OTP_LOGIN,
            ScopeInterface::SCOPE_STORE
        );
    }


    /**  @return string */

    public function getSmsProfileApiGateWay($storeId = null)
    {
        if ($storeId == null) {
            return $this->scopeConfig->getValue(
                self::XML_PATH_API_GATEWAY,
                ScopeInterface::SCOPE_STORE
            );
        }
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_GATEWAY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }


    /**  @return string */

    public function getSmsProfileTwilioAccountSId($storeId = null)
    {
        if ($storeId == null) {
            return $this->scopeConfig->getValue(
                self::XML_PATH_TWILIO_ACCOUNT_SID,
                ScopeInterface::SCOPE_STORE
            );
        }
        return $this->scopeConfig->getValue(
            self::XML_PATH_TWILIO_ACCOUNT_SID,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    

     /**  @return string */

    public function getSmsProfileTwilioAccountToken($storeId = null)
    {
        if ($storeId == null) {
            return $this->scopeConfig->getValue(
                self::XML_PATH_TWILIO_ACCOUNT_TOKEN,
                ScopeInterface::SCOPE_STORE
            );
        }
        return $this->scopeConfig->getValue(
            self::XML_PATH_TWILIO_ACCOUNT_TOKEN,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }


    /**  @return string */

    public function getSmsProfileTwilioPhoneNumber($storeId = null)
    {
        if ($storeId == null) {
            return $this->scopeConfig->getValue(
                self::XML_PATH_TWILIO_PHONE,
                ScopeInterface::SCOPE_STORE
            );
        }
        return $this->scopeConfig->getValue(
            self::XML_PATH_TWILIO_PHONE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }


    /** @return string */
    
    public function getSmsProfileTwilioServiceId()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TWILIO_SERVICE_ID,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** @return boolean */

    public function getSmsProfileOnCheckoutPage()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENABLE_ON_CHECKOUT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return string */

    public function getSmsProfileDefaultCountry($storeId = null)
    {
        if ($storeId == null) {
            return $this->scopeConfig->getValue(
                self::XML_PATH_DEFAULT_COUNTRY,
                ScopeInterface::SCOPE_STORE
            );
        }
        return $this->scopeConfig->getValue(
            self::XML_PATH_DEFAULT_COUNTRY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**  @return string */

    public function getBulkSmsUserName($storeId = null)
    {
        if ($storeId == null) {
            return $this->scopeConfig->getValue(
                self::XML_PATH_BULKSMS_USER,
                ScopeInterface::SCOPE_STORE
            );
        }
        return $this->scopeConfig->getValue(
            self::XML_PATH_BULKSMS_USER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**  @return string */
    
    public function getBulkSmsPassword($storeId = null)
    {
        if ($storeId == null) {
            return $this->scopeConfig->getValue(
                self::XML_PATH_BULKSMS_PASSWORD,
                ScopeInterface::SCOPE_STORE
            );
        }
        return $this->scopeConfig->getValue(
            self::XML_PATH_BULKSMS_PASSWORD,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

     /**  @return string */
    
    public function getBulkSmsUrl($storeId = null)
    {
        if ($storeId == null) {
            return $this->scopeConfig->getValue(
                self::XML_PATH_BULKSMS_URL,
                ScopeInterface::SCOPE_STORE
            );
        }
        return $this->scopeConfig->getValue(
            self::XML_PATH_BULKSMS_URL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }



     /** @return bool */
    
    public function isCustomerCountryEnabled($storeId = null)
    {
        if ($this->getSmsProfileApiGateWay() == "Other") {
            if ($this->getSmsProfileApiCountryRequired($storeId)) {
                if ($storeId == null) {
                    return $this->scopeConfig->getValue(
                        self::XML_PATH_CUSTOMER_COUNTRY,
                        ScopeInterface::SCOPE_STORE
                    );
                }
                return $this->scopeConfig->getValue(
                    self::XML_PATH_CUSTOMER_COUNTRY,
                    ScopeInterface::SCOPE_STORE,
                    $storeId
                );
            } else {
                return (bool)false;
            }
        } else {
            if ($storeId == null) {
                return $this->scopeConfig->getValue(
                    self::XML_PATH_CUSTOMER_COUNTRY,
                    ScopeInterface::SCOPE_STORE
                );
            }
            return $this->scopeConfig->getValue(
                self::XML_PATH_CUSTOMER_COUNTRY,
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
        }
    }

    public function getDefaultCustomerCountry()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DEFAULT_CUSTOMER_COUNTRY,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getResendTime()
    {
        $resendTime = $this->scopeConfig->getValue(
            self::XML_PATH_RESEND_TIME,
            ScopeInterface::SCOPE_STORE
        );

        if ($resendTime < 5) {
            $resendTime = 5;
        }
        return $resendTime;
    }

    public function getSendOtpVia()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SEND_OTP_VIA,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getAvailableCountries()
    {
        $countries = $this->scopeConfig->getValue(
            self::XML_PATH_AVAILABLE_COUNTRIES,
            ScopeInterface::SCOPE_STORE
        );

        $countryArray = [];
        if ($countries) {
            $availableCountries = explode(",", $countries);
            foreach ($availableCountries as $country) {
                $countryArray[] = '"'.$country.'"';
            }
            return implode(",", $countryArray);
        }
        return null;
    }

    public function getAvailableCountriesCheckout()
    {
        $countries = $this->scopeConfig->getValue(
            self::XML_PATH_AVAILABLE_COUNTRIES,
            ScopeInterface::SCOPE_STORE
        );

        if ($countries) {
            $availableCountries = explode(",", $countries);
            return $availableCountries;
        }
        return null;
    }

    /** @return array */
    
    public function getSmsProfileNotifyToMail()
    {
        $toMail = $this->scopeConfig->getValue(
            self::XML_PATH_NOTIFY_TO_MAIL,
            ScopeInterface::SCOPE_STORE
        );

        $toMail = explode(",", $toMail);
        return $toMail;
    }

    /** @return string */
    
    public function getSmsProfileNotifyFromMail()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NOTIFY_FROM_MAIL,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** @return string */
    
    public function getSmsProfilePhoneMaxLength()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHONE_MAX,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** @return string */
    
    public function getSmsProfilePhoneMinLength()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHONE_MIN,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getSmsProfilePhoneValidationClass()
    {
        $maxLength = $this->getSmsProfilePhoneMaxLength();
        $minLength = $this->getSmsProfilePhoneMinLength();
        $validateMaxLength ='';
        $validateMinLength ='';
        if ($maxLength) {
            $validateMaxLength ='maximum-length-'.$maxLength;
        }
        if ($minLength) {
            $validateMinLength ='minimum-length-'.$minLength;
        }

        if ($validateMaxLength != "" || $validateMinLength != "") {
            $validateMaxLength .=" validate-length";
        }

        return 'validate-number profile-validate-length '.$validateMaxLength .' '.$validateMinLength;
    }

    /** return string */

    public function getOtpFormat()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_OTP_FORMAT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** return string */

    public function getOtpLength()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_OTP_LENGTH,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** return string */

    public function getPhoneNote()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHONE_NOTICE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** return string */

    public function getAddressType()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHONE_EAV,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** return string */

    public function getTimeoutSeconds()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SMSPROFILELOG_TIMEOUT,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function generateOTP()
    {
        $length = $this->getOtpLength();
        $format = $this->getOtpFormat();
        if ($format == 'alphanum') {
            $characters = array_merge(range('A', 'Z'), range('a', 'z'), range('0', '9'));
        } elseif ($format == 'alpha') {
            $characters = array_merge(range('A', 'Z'), range('a', 'z'));
        } else {
            $characters = array_merge(range('0', '9'));
        }

        $otp_string = '';
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
             $otp_string .= $characters[random_int(0, $max)];
        }

        return $otp_string;
    }

    /**  @return string */
    public function getCurrentStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

  

     /**  @return string */

    public function getSmsProfileApiUrl($storeId = null)
    {
        if ($storeId == null) {
            $storeId = $this->storeManager->getStore()->getId();
        }
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_URL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    
    /**  @return array */

    public function getSmsProfileApiCredential($storeId = null)
    {
        if ($storeId == null) {
            $storeId = $this->storeManager->getStore()->getId();
        }
        $credential_array = [];
        $credential = $this->scopeConfig->getValue(
            self::XML_PATH_API_CREDENTIAL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $paramArray =  explode(',', $credential ?? '');
        if (isset($paramArray[0])) {
            $credential_array[preg_replace('/^([^:]*).*$/', '$1', $paramArray[0])]  = substr($paramArray[0], strpos($paramArray[0], ":") + 1);
        }
        if (isset($paramArray[1])) {
            $credential_array[preg_replace('/^([^:]*).*$/', '$1', $paramArray[1])]  = substr($paramArray[1], strpos($paramArray[1], ":") + 1);
        }
       
        

        return $credential_array;
    }


    /**  @return string */

    public function getSmsProfileApiTo($storeId = null)
    {
        if ($storeId == null) {
            return $this->scopeConfig->getValue(
                self::XML_PATH_API_TO,
                ScopeInterface::SCOPE_STORE
            );
        }
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_TO,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }


    /**  @return string */

    public function getSmsProfileApiSmsBody($storeId = null)
    {
        if ($storeId == null) {
            return $this->scopeConfig->getValue(
                self::XML_PATH_API_BODY,
                ScopeInterface::SCOPE_STORE
            );
        }
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_BODY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    
    /**  @return string */

    public function getSmsProfileApiCountryRequired($storeId = null)
    {
        if ($storeId == null) {
            return $this->scopeConfig->getValue(
                self::XML_PATH_API_COUNTRY,
                ScopeInterface::SCOPE_STORE
            );
        }
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_COUNTRY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    

    /**  @return array */
    public function getSmsProfileApiParams($storeId = null)
    {
        if ($storeId == null) {
            $storeId = $this->storeManager->getStore()->getId();
        }
        $param = [];
        $data = $this->scopeConfig->getValue(
            self::XML_PATH_API_PARAMS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $dataArray =  explode(',', $data);
        foreach ($dataArray as $dataArray) {
            $param[preg_replace('/^([^:]*).*$/', '$1', $dataArray)] = substr($dataArray, strpos($dataArray, ":") + 1);
        }
        return $param;
    }


    /**  @return array */
    public function getSmsProfileApiUpdateParams()
    {
        $param = [];
        $data = $this->scopeConfig->getValue(
            self::XML_PATH_API_UPDATEPARAMS,
            ScopeInterface::SCOPE_STORE
        );
        if ($data) {
            $dataArray =  explode(',', $data);
            foreach ($dataArray as $dataArray) {
                $param[preg_replace('/^([^:]*).*$/', '$1', $dataArray)] = substr($dataArray, strpos($dataArray, ":") + 1);
            }
        }
        return $param;
    }

    /**  @return array */
    public function getSmsProfileApiParamsForPromotional()
    {
        $param = $this->getSmsProfileApiParams();
        $updateparam = $this->getSmsProfileApiUpdateParams();
        $arrayKeys = array_keys($updateparam);
        foreach ($arrayKeys as $key) {
            if (array_key_exists($key, $param)) {
                $param[$key] = $updateparam[$key] ;
                if ($updateparam[$key] === 'false') {
                    unset($param[$key]);
                }
            }
        }
        return $param;
    }
    /**  @return string */
    public function getSmsProfileApiUrlForPromotional()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_URL_PROMOTIONAL,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return string */
    public function getSmsProfileApiSmsFetchUrl()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_FETCHURL,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return array */
    public function getSmsProfileApiProcessStatus()
    {
        $data = $this->scopeConfig->getValue(
            self::XML_PATH_API_PROCESS_STATUS,
            ScopeInterface::SCOPE_STORE
        );

        return explode(',', $data ?? '');
    }

    /**  @return array */
    public function getSmsProfileApiFailureStatus()
    {
        $data =  $this->scopeConfig->getValue(
            self::XML_PATH_API_FAIL_STATUS,
            ScopeInterface::SCOPE_STORE
        );

        return explode(',', $data);
    }

    /**  @return string */
    public function getSmsProfileApiSmsErrorKey()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_API_ERROR,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return string */
    public function getSmsProfileOTPExpiry()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_OTP_EXPIRY,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getSmsProfileMailPhoneValidationClass()
    {
        $maxLength = $this->getSmsProfilePhoneMaxLength();
        $minLength = $this->getSmsProfilePhoneMinLength();
        $validateMaxLength ='';
        $validateMinLength ='';
        if ($maxLength) {
            $validateMaxLength ='maximum-length-'.$maxLength;
        }
        if ($minLength) {
            $validateMinLength ='minimum-length-'.$minLength;
        }

        if ($validateMaxLength != "" || $validateMinLength != "") {
            $validateMaxLength .=" validate-length";
        }
        
        return 'profile-validate-mobile-mail '.$validateMaxLength .' '.$validateMinLength;
    }

    /**
     * Prepare telephone field config according to the Magento default config
     * @param $addressType
     * @param string $method
     * @return array
     */
    public function telephoneFieldConfig($addressType, $method = '')
    {
        if ($addressType=='billingAddress') {
            $telephoneTemplate = 'Magedelight_SMSProfile/form/element/billing-telephone';
        } else {
            $telephoneTemplate = 'Magedelight_SMSProfile/form/element/telephone';
        }
        return  [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'customScope' => $addressType . $method,
                'customEntry' => null,
                'template' => 'ui/form/field',
                'elementTmpl' => $telephoneTemplate,
                'tooltip' => [
                    'description' => 'For delivery questions. Mobile Number with Country code 5XXXXXXXX',
                    'tooltipTpl' => 'ui/form/element/helper/tooltip'
                ],
            ],
            'dataScope' => $addressType . $method . '.telephone',
            'dataScopePrefix' => $addressType . $method,
            'label' => __('Phone Number'),
            'provider' => 'checkoutProvider',
            'sortOrder' => 120,
            'validation' => [
                "required-entry"    => false,
                /*"min_tel_digit_length"   => $this->getSmsProfilePhoneMinLength(),
                "max_tel_digit_length"   => $this->getSmsProfilePhoneMaxLength(),*/
                "validate-intl-phone" => true,
                "validate-number"=>true,
            ],
            'options' => [],
            'filterBy' => null,
            'customEntry' => null,
            'visible' => false,
            'focused' => false,
        ];
    }
    /* SMS Notification*/

    /**  @return bool */

    public function getSmsLogEnable()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SMSLOG_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** @return bool */

    public function getNotifyFailureStatus()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ADMIN_NOTIFYFAILURE,
            ScopeInterface::SCOPE_STORE
        );
    }

     /**  @return string */

    public function getNotifySenderMail()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ADMIN_NOTIFYFROMMAIL,
            ScopeInterface::SCOPE_STORE
        );
    }

     /**  @return array */

    public function getNotifyToMail()
    {
        $toMail =  $this->scopeConfig->getValue(
            self::XML_PATH_ADMIN_NOTIFYTOMAIL,
            ScopeInterface::SCOPE_STORE
        );
        $toMail = explode(",", $toMail);
        return $toMail;
    }

    /**  @return array */

    public function getCustomerEvents()
    {
        $customer_events = $this->scopeConfig->getValue(
            self::XML_PATH_CUSTOMER_EVENTS,
            ScopeInterface::SCOPE_STORE
        );

        $customer_events_array = explode(",", $customer_events);
        return $customer_events_array;
    }

    /**  @return string */

    public function geSelectedCustomerNumber()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SELECT_CUSTOMER_NO,
            ScopeInterface::SCOPE_STORE
        );
    }


    /**  @return string */

    public function setOrderMesageText($message, $data)
    {
        $keywords   = [
            '{firstname}',
            '{lastname}',
            '{order_id}',
            '{total}',
            '{orderitem}',
            '{store}'
        ];
        $message = str_replace($keywords, $data, $message);
        return $message;
    }

    /**  @return array */
    public function getOrderData($order)
    {
        $_order = $order->getOrder($order);
        if ($order->getGrandTotal()) {
            $total =$order->getGrandTotal();
        } else {
            $payment = $order->getPayment();
            if ($payment) {
                $total = $order->getPayment()->getAmountOrdered();
            } else {
                $total = 0;
            }
        }
        
        $currency_code = $order->getOrderCurrencyCode();
        $currency_symbol = $this->localecurrency->getCurrency($currency_code)
                                ->getSymbol();

        $orderData          =   [
            'firstname'     =>  $order->getCustomerFirstname(),
            'lastname'      =>  $order->getCustomerLastname(),
            'order_id'      =>  ($_order) ? $_order->getIncrementId() : $order->getIncrementId(),
            'total'         =>  $currency_symbol.$total,
            'orderitem'     =>  $this->getOrderedItems($order->getAllItems()),
            'store'         => $this->getCurrentStoreName()
        ];
        return $orderData;
    }

     /**  @return string */
    public function getOrderedItems($items)
    {
        $order_items = [];
        foreach ($items as $item) {
            $order_items[] = $item->getName();
        }

        return implode(",", $order_items);
    }

     /**  @return string */
    public function getCurrentStoreName()
    {
        return $this->storeManager->getStore()->getName();
    }

     /** @return bool */

    public function getCronStatus()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SMSLOG_CRON_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

     /** @return bool */

    public function getNotifyAdmin()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NOTIFYADMIN_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return string */

    public function getAdminContactNumbers()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ADMIN_CONTACT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return array */

    public function getAdminEvents()
    {
        $admin_events = $this->scopeConfig->getValue(
            self::XML_PATH_ADMIN_EVENTS,
            ScopeInterface::SCOPE_STORE
        );

        $admin_events_array = explode(",", $admin_events ?? '');
        return $admin_events_array;
    }

    public function getPhoneValidationClass()
    {
        $maxLength = $this->getCustomerNumberMaxLength();
        $minLength = $this->getCustomerNumberMinLength();
        $validateMaxLength ='';
        $validateMinLength ='';
        if ($maxLength) {
            $validateMaxLength ='maximum-length-'.$maxLength;
        }
        if ($minLength) {
            $validateMinLength ='minimum-length-'.$minLength;
        }

        if ($validateMaxLength != "" || $validateMinLength != "") {
            $validateMaxLength .=" validate-length";
        }
        
        return 'validate-number custom-validate-length '.$validateMaxLength .' '.$validateMinLength;
    }

    /**  @return string */

    public function getCustomerNumberMaxLength()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHONE_MAX_LENGTH,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return string */

    public function getCustomerNumberMinLength()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHONE_MIN_LENGTH,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return string */

    public function getNoticeBelowTelephone()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PHONE_NOTICE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** @return bool */

    public function getAutoGenerateLogin()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AUTO_GENERATE_LOGIN,
            ScopeInterface::SCOPE_STORE
        );
    }


    /** @return bool */

    public function getAutoVerifyOTP()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AUTO_VERIFY_OTP,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return string */

    public function getMobileNumberLimit()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MOBILE_NUMBER_LIMIT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** @return bool */

    public function getOTPResendLimitEnable()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_OTP_RESEND_LIMIT_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return string */

    public function getOTPResendLimit()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_OTP_RESEND_LIMIT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**  @return string */

    public function getOTPResendTime()
    {
        /*return $this->scopeConfig->getValue(
            self::XML_PATH_OTP_RESEND_TIME,
            ScopeInterface::SCOPE_STORE
        );*/
        $resendTime = $this->scopeConfig->getValue(
            self::XML_PATH_RESEND_TIME,
            ScopeInterface::SCOPE_STORE
        );

        if (!$this->getOTPResendLimitEnable()) {
            $resendTime = 5;
        } elseif ($resendTime < 5) {
            $resendTime = 5;
        }
        return $resendTime;
    }

   /** Set Custom Cookie using Magento 2 */
    public function setCustomCookie($cookieName, $cookieValue, $expireTime)
    {
        $publicCookieMetadata = $this->_cookieMetadataFactory->createPublicCookieMetadata();
        $duration=(60*$expireTime);
        $publicCookieMetadata->setDuration($duration);
        $publicCookieMetadata->setPath('/');
        $publicCookieMetadata->setHttpOnly(false);

        return $this->_cookieManager->setPublicCookie(
            $cookieName,
            $cookieValue,
            $publicCookieMetadata
        );
    }

    /** Get Custom Cookie using */
    public function getCustomCookie($cookieName)
    {
        return $this->_cookieManager->getCookie(
            $cookieName
        );
    }

    /** Get Current date */
    public function getCurrentDate()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objDate = $objectManager->create('Magento\Framework\Stdlib\DateTime\DateTime');
        return $objDate->gmtDate();
    }

    /** @return string */

    public function getAuthorizationHeader()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AUTHORIZATION_HEADER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /** @return bool */
    public function getSmsProfileEmailOptionalOnSignUp()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAILREQUIRED_SIGNUP,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getStoreDomain()
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl();
        $baseUrl = str_replace("http://", "", $baseUrl);
        $baseUrl = str_replace("https://", "", $baseUrl);
        $baseUrl = str_replace("www.", "", $baseUrl);
        $baseUrl = str_replace("/", "", $baseUrl);
        return $baseUrl;
    }

    /** @return bool */
    public function verifyMobileBeforeRegistration()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MOBILEVERIFICATION_SIGNUP,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function redirectOldCustomer()
    {
        
        return $this->scopeConfig->getValue(
            self::XML_PATH_MOBILE_CUSTOMER_REDIRECT,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function loginPopupEnable()
    {
        
        return $this->scopeConfig->getValue(
            self::XML_PATH_MOBILE_LOGIN_POPUP_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getClientIpAddress()
    {

        /*$ip ="";
        // if user from the share internet
        if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        //if user is from the proxy
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        //if user is from the remote address
        else{
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip; */
    }

    /**
     * Return template id according to store
     *
     * @return mixed
     */
    public function getSMSAlertTemplateId()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SMS_ALERT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Return template id according to store
     *
     * @return mixed
     */
    public function getSMSSendOtp()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SEND_OTP,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Return template id according to store
     *
     * @return mixed
     */
    public function getSMSNotificationTemplateId()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SMS_NOTIFICATION,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getPhoneValidation()
    {

        $phoneValidation = $this->scopeConfig->getValue(
            self::XML_PATH_PHONE_VALIDATION,
            ScopeInterface::SCOPE_STORE
        );

        if ($phoneValidation == '' || $phoneValidation == null) {
            return;
        }

        $unserializeData = $this->serialize->unserialize($phoneValidation);

        $phoneValidationData = array();
        foreach ($unserializeData as $key => $row) {
            $phoneValidationData[] = ['country'=>$row['country'],'digit'=>$row['digit']];
        }

        return $phoneValidationData;
    }

    /**
     * Return recaptcha status according to store
     *
     * @return boolean
     */
    public function getRecaptchaStatus()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_RECAPTCHA_STATUS,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Return recaptcha site key according to store
     *
     * @return string
     */
    public function getRecaptchaSiteKey()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_RECAPTCHA_SITEKEY,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Return recaptcha secret key according to store
     *
     * @return string
     */
    public function getRecaptchaSecretKey()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_RECAPTCHA_SECRETKEY,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Return recaptcha forms according to store
     *
     * @return string
     */
    public function getRecaptchaForms()
    {
        $forms = $this->scopeConfig->getValue(
            self::XML_PATH_RECAPTCHA_FORMS,
            ScopeInterface::SCOPE_STORE
        );
        $recaptcha_form_array = [];
        $recaptcha_form_array = explode(",", $forms ?? '');
        return $recaptcha_form_array;
    }

     /** @return bool */

    public function getGuestCheckoutLogin()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GUEST_CHECKOUT_LOGIN,
            ScopeInterface::SCOPE_STORE
        );
    }
}
