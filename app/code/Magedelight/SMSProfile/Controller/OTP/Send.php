<?php
/**
 * Magedelight
 * Copyright (C) 2023 Magedelight <info@magedelight.com>
 *
 * @category  Magedelight
 * @package   Magedelight_SMSProfile
 * @copyright Copyright (c) 2023 Mage Delight (http://www.magedelight.com/)
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author    Magedelight <info@magedelight.com>
 */

namespace Magedelight\SMSProfile\Controller\OTP;

use Magedelight\SMSProfile\Api\SMSProfileTemplatesRepositoryInterface;
use Magedelight\SMSProfile\Helper\Data as HelperData;
use Magedelight\SMSProfile\Model\ResourceModel\SMSProfileOtp\CollectionFactory as SmsOtpCollectionFactory;
use Magedelight\SMSProfile\Model\SMSProfileOtpFactory;
use Magedelight\SMSProfile\Model\SMSProfileService;
use Magedelight\SMSProfile\Model\SMSProfileOtpAttemptFactory;
use Magedelight\SMSProfile\Model\ResourceModel\SMSProfileOtpAttempt\CollectionFactory as SmsOtpAttemptCollectionFactory;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\JsonFactory as ResultJsonFactory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Send extends Action
{
    /** @var HelperData */
    private $datahelper;

    /** @var SMSProfileService */
    private $smsProfileService;

    /** @var SMSProfileTemplatesRepositoryInterface */
    private $smsProfileTemplates;

    /** @var SMSProfileOtpFactory  */
    private $smsProfileOtp;

    /** @var SMSProfileOtpAttemptFactory  */
    private $smsProfileOtpAttempt;

    /** @var ResultJsonFactory */
    private $resultJsonFactory;

    /**  @var TimezoneInterface */
    private $timezone;

    /** @var SmsOtpCollectionFactory */
    private $collection;
     /** @var SmsOtpAttemptCollectionFactory */
    private $collectionAttempt;

    /** @var CollectionFactory */
    private $customerCollection;

    /** @var TransportBuilder */
    private $transportBuilder;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var StateInterface */
    private $inlineTranslation;

    /** @var StoreManagerInterface */
    private $storeManager;
    /**
     * @var Session
     */
    private $session;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var \\Magento\Framework\Stdlib\DateTime\DateTime dateTime
     */
    private $dateTime;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * Send constructor.
     * @param Context $context
     * @param TimezoneInterface $timezone
     * @param SMSProfileService $smsProfileService
     * @param SMSProfileOtpFactory $smsProfileOtp
     * @param SmsOtpCollectionFactory $collection
     * @param ResultJsonFactory $resultJsonFactory
     * @param SMSProfileTemplatesRepositoryInterface $smsProfileTemplates
     * @param CollectionFactory $customerCollection
     * @param HelperData $dataHelper
     * @param TransportBuilder $transportBuilder
     * @param ScopeConfigInterface $scopeConfig
     * @param StateInterface $inlineTranslation
     * @param StoreManagerInterface $storeManager
     * @param Session $session
     */
    public function __construct(
        Context $context,
        TimezoneInterface $timezone,
        SMSProfileService $smsProfileService,
        SMSProfileOtpFactory $smsProfileOtp,
        SmsOtpCollectionFactory $collection,
        SmsOtpAttemptCollectionFactory $collectionAttempt,
        SMSProfileOtpAttemptFactory $smsProfileOtpAttempt,
        ResultJsonFactory $resultJsonFactory,
        SMSProfileTemplatesRepositoryInterface $smsProfileTemplates,
        CollectionFactory $customerCollection,
        HelperData $dataHelper,
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig,
        StateInterface $inlineTranslation,
        StoreManagerInterface $storeManager,
        Session $session,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
    ) {
        parent::__construct($context);
        $this->datahelper = $dataHelper;
        $this->collection = $collection;
        $this->collectionAttempt = $collectionAttempt;
        $this->smsProfileService = $smsProfileService;
        $this->smsProfileTemplates = $smsProfileTemplates;
        $this->smsProfileOtp = $smsProfileOtp;
        $this->smsProfileOtpAttempt = $smsProfileOtpAttempt;
        $this->timezone = $timezone;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerCollection = $customerCollection;
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->inlineTranslation = $inlineTranslation;
        $this->storeManager = $storeManager;
        $this->session = $session;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->dateTime = $dateTime;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $_data =  $this->getRequest()->getParams();
        $result = $this->resultJsonFactory->create();
        if (isset($_data['email']) && $_data['email']=="") {
            unset($_data['email']);
        }
        if ($this->datahelper->getRecaptchaStatus() && in_array($_data['formType'], $this->datahelper->getRecaptchaForms())) {
            $secretKey  = $this->datahelper->getRecaptchaSecretKey();
            $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secretKey.'&response='.$_data['captchaResponse']);
            $responseData = json_decode($verifyResponse);
            if (!$responseData->success) {
                $result->setData(['Success' => __('Robot verification failed, please try again.')]);
                return $result;
            }
        }
        
        $otp = $this->getGeneratedOTP();
        $minutes = $this->datahelper->getSmsProfileOTPExpiry();
        $otpAttemptLimit=$this->datahelper->getOTPResendLimit();
        $resendLimitTime=$this->datahelper->getOTPResendTime();
        $now = $this->timezone->date(null, null, false)->format('Y-m-d H:i:s');
        $now2 = $this->timezone->date(null, null, false)->modify('-' . $minutes . 'minute')->format('Y-m-d H:i:s');
        $_smsProfileOtp = $this->collection->create();
        if (isset($_data['email'])) {
            $_smsProfileOtp->addFieldToFilter('customer_mobile', $_data['email']);
        } else {
            $_smsProfileOtp->addFieldToFilter('customer_mobile', $this->getToNumber());
        }
        
        $_smsProfileOtp->addFieldToFilter('created_at', ['from' => $now2, 'to' => $now]);
        $_smsProfileOtp->addFieldToFilter('created_at', ['gteq' => $now2, 'lteq' => $now]);
        $_smsProfileOtp->setOrder('entity_id', 'DESC');
        $_smsProfileOtp->getFirstItem();
        $existCode=0;
        $attemptId=0;
        $resetAttempt=0;
        $resendCountTime=null;
        $expireTime=$this->datahelper->getOTPResendTime();
        
        //Check is Mobile number existing or not:
        $_smsProfileOtpAttempt = $this->collectionAttempt->create();
        if (isset($_data['email'])) {
            $_smsProfileOtpAttempt->addFieldToFilter('customer_mobile', $_data['email']);
        } else {
            $_smsProfileOtpAttempt->addFieldToFilter('customer_mobile', $this->getToNumber());
        }
        $_smsProfileOtpAttempt->getFirstItem();
       
        if ($_smsProfileOtpAttempt->getSize() > 0) {
            $SmsOTPAttempt = $_smsProfileOtpAttempt->getLastItem();
            $resendCountTime = $SmsOTPAttempt->getResendCountTime();
            //Left Minutes :
            if ($resendCountTime != null) {
                $from_time = strtotime($now);
                $to_time = strtotime($resendCountTime);
                $diff_minutes = round(abs($from_time - $to_time) / 60, 2);
                $diff_minutes = ceil($diff_minutes);
                $leftMinutes=($resendLimitTime - $diff_minutes);
                if ($leftMinutes > 0) {
                    $result = $this->resultJsonFactory->create();
                    $result->setData(['Success' => __('OTP resend limit exhausted. Kindly try after').' '.$leftMinutes.' '.__('Minutes'),'resend_link_count' => $SmsOTPAttempt->getAttempCount(),'attempt_limit_exhausted'=>1]);
                    return $result;
                } else {
                    $resetAttempt=1;
                }
            }
            
            $existCode = $SmsOTPAttempt->getAttempCount();
            $attemptId = $SmsOTPAttempt->getId();
        }

        $existCode = $existCode+1;
        if (in_array($this->getTransactionType(), ['customer_login_otp','forgot_password_otp'])) {
            if (isset($_data['email'])) {
                if ($this->validateCustomerByEmail($_data['email'])) {
                    $result->setData(['Success' => __('Account with this number doesn\'t exist')]);
                    return $result;
                }
            } else {
                if ($this->validateCustomerByPhone($this->getMobileNumber(), $this->getCountryCode())) {
                    $result = $this->resultJsonFactory->create();
                    $result->setData(['Success' => __('We couldn\'t find an account associated with this number. Please create an account to proceed with checkout.')]);
                    return $result;
                }
            }
        }
        if (in_array($this->getTransactionType(), ['customer_signup_otp','customer_account_edit_otp'])) {
            if ($this->registerCustomerByPhone($this->getMobileNumber(), $this->getCountryCode())) {
                $result = $this->resultJsonFactory->create();
                $result->setData(['Success' => __('Account with this number already exist')]);
                return $result;
            }
        }

        if (!isset($_data['email'])) {
            $sms = $this->smsProfileTemplates->getByEventType(
                $this->getTransactionType(),
                $this->datahelper->getCurrentStoreId()
            );
        }


        if (isset($sms) && is_string($sms) && !isset($_data['email'])) {
            $result = $this->resultJsonFactory->create();
            $result->setData(['Success' => __('Not able find SMS template')]);
        } else {
            $data['otpCode'] = $otp;
            if ($_smsProfileOtp->getSize() > 0) {
                $SmsOTP = $_smsProfileOtp->getLastItem();
                //$data['otpCode'] = $SmsOTP->getOtpCode();
            }
            $result = $this->resultJsonFactory->create();
            if (!isset($_data['email'])) {
                $_message = $sms->getData('template_content');
                $message = $this->setSMSBody($_message, $data);
                $this->smsProfileService->setToNumber($this->getToNumber());
                $this->smsProfileService->setMessageContent($message);
                $this->smsProfileService->setTransactionType($this->getTransactionType());
                $this->smsProfileService->setApiVersion($this->getApiVersion());
            }
            try {
                if (isset($_data['email'])) {
                    $this->callEmailSending('', '', $otp, $_data['email']);
                } else {
                    if (in_array($this->datahelper->getSendOtpVia(), ['sms', 'both'])) {
                        $this->callSmsSending();
                    }
                    if (!in_array($this->getTransactionType(), ['customer_signup_otp','cod_otp','customer_account_edit_otp'])) {
                        if (in_array($this->datahelper->getSendOtpVia(), ['email', 'both'])) {
                            $this->callEmailSending($this->getMobileNumber(), '', $otp);
                        }
                    }
                    if ($this->getTransactionType() == 'customer_account_edit_otp' && $this->session->isLoggedIn()) {
                        $customer = $this->session->getCustomer();
                        $this->callEmailSending($this->getMobileNumber(), $customer, $otp);
                    }
                }

                $smsProfileOtp =  $this->smsProfileOtp->create();
                $smsProfileOtp->setOtpCode($otp);
                if (isset($_data['email'])) {
                    $smsProfileOtp->setCustomerMobile($_data['email']);
                } else {
                    $smsProfileOtp->setCustomerMobile($this->getToNumber());
                }
                $smsProfileOtp->setResend($this->getIdResend());
                $smsProfileOtp->setCreatedAt($now);
                $smsProfileOtp->setUpdatedAt($now);
                $smsProfileOtp->save();
                if ($_smsProfileOtp->getSize() == 0) {
                    try {
                    //Set Attempt row:
                        $_smsProfileOtpAttempt->getSize();
                        if ($_smsProfileOtpAttempt->getSize() == 0) {
                            $smsProfileOtpAttempt=$this->smsProfileOtpAttempt->create();
                            if (isset($_data['email'])) {
                                $smsProfileOtpAttempt->setCustomerMobile($_data['email']);
                            } else {
                                $smsProfileOtpAttempt->setCustomerMobile($this->getToNumber());
                            }
                            $smsProfileOtpAttempt->setCustomerId(1);
                            $smsProfileOtpAttempt->setAttempCount($existCode);
                            $smsProfileOtpAttempt->setResendCountTime(null);
                            $smsProfileOtpAttempt->save();
                        }
                    } catch (\Exception $e) {
                        $result->setData(['fail' => __($e->getMessage())]);
                    }
                }


                //Update Attempt row:
                if ($_smsProfileOtpAttempt->getSize() > 0 && $attemptId != 0) {
                    $_smsProfileOtpAttemptUpdate=$this->smsProfileOtpAttempt->create();
                    $_smsProfileOtpAttemptUpdateModel=$_smsProfileOtpAttemptUpdate->load($attemptId);
                    $_smsProfileOtpAttemptUpdateModel->setAttempCount($existCode);
                    
                    if ($existCode >= $otpAttemptLimit && $resendCountTime==null) {
                        $_smsProfileOtpAttemptUpdateModel->setResendCountTime($now);
                    } elseif ($resetAttempt==1) {
                        $_smsProfileOtpAttemptUpdateModel->setAttempCount(1);
                        $_smsProfileOtpAttemptUpdateModel->setResendCountTime(null);
                        $existCode=1;
                    }
                    $_smsProfileOtpAttemptUpdateModel->save();
                }
                $otpMessage='';

                if ($this->datahelper->getSendOtpVia() =='email') {
                    $otpMessage= __('Kindly check your registered email for OTP.');
                } elseif ($this->datahelper->getSendOtpVia() =='sms') {
                     $otpMessage= __('Kindly check your mobile SMS App for OTP.');
                } else {
                    $otpMessage= __('Kindly check your email or Mobile SMS for OTP.');
                }
                $result->setData(['Success' => __('success'),'resend_link_count' => $existCode,'otp_message'=>$otpMessage]);
            } catch (\Exception $e) {
                $result->setData(['Success' => __('Not able to send SMS')]);
            }
        }
        
        return $result;
    }

    /**
     * @param $message
     * @param $data
     * @return mixed
     */
    public function setSMSBody($message, $data)
    {
        $keywords   = [
            '{otpCode}'
        ];
        $message = str_replace($keywords, $data, $message);
        return $message;
    }

    /**
     * @return string
     */
    private function getApiVersion()
    {
        return  $this->datahelper->getSmsProfileApiGateWay();
    }

    /**
     * @return mixed
     */
    private function getTransactionType()
    {
        $_data =  $this->getRequest()->getParams();
        return $_data['eventType'];
    }

    private function callSmsSending()
    {
        if ($this->getApiVersion() == 'Twilio Api Service') {
            $this->smsProfileService->sendOTPSmsWithTwilio();
        } else {
            if ($this->datahelper->getApiReauestResponseXML()) {
                $this->smsProfileService->sendSmsProfileOTPViaOtherServicesXML();
            } else {
                $this->smsProfileService->sendSmsProfileOTPViaOtherServices();
            }
        }
    }

    /**
     * @return string
     */
    private function getGeneratedOTP()
    {
        return $this->datahelper->generateOTP();
    }

    /**
     * @return string
     */
    private function getToNumber()
    {
        $_data =  $this->getRequest()->getParams();
        if ($this->datahelper->isCustomerCountryEnabled() && isset($_data['countrycode'])) {
            return $_data['countrycode'] . $_data['mobile'];
        }
        return $_data['mobile'];
    }

    /**
     * @return mixed
     */
    private function getMobileNumber()
    {
        $_data =  $this->getRequest()->getParams();
        return $_data['mobile'];
    }

    /**
     * @return |null
     */
    private function getCountryCode()
    {
        $_data =  $this->getRequest()->getParams();
        if (isset($_data['countrycode'])) {
            return $_data['countrycode'];
        }
        return null;
    }

    /**
     * @return mixed
     */
    private function getIdResend()
    {
        $_data =  $this->getRequest()->getParams();
        return $_data['resend'];
    }

    /**
     * @param $phone
     * @param null $code
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validateCustomerByPhone($phone, $code = null)
    {
        $customerCollection = $this->customerCollection->create();
        if ($code == null) {
            $customerCollection->addAttributeToSelect('*')
                           ->addAttributeToFilter('customer_mobile', ['eq' => $phone])
                           ->load();
        } else {
            $customerCollection->addAttributeToSelect('*')
                           ->addAttributeToFilter('customer_mobile', ['eq' => $phone])
                           ->addAttributeToFilter('countryreg', ['eq' => $code])
                           ->load();
        }
        if ($customerCollection->getSize() == 0) {
            return true;
        }
        return false;
    }

    /**
     * @param $phone
     * @param null $code
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validateCustomerByEmail($email)
    {
        $customerCollection = $this->customerCollection->create();
        $customerCollection->addAttributeToSelect('*')
                           ->addAttributeToFilter('email', ['eq' => $email])
                           ->load();
        if ($customerCollection->getSize() == 0) {
            return true;
        }
        return false;
    }

    /**
     * @param $phone
     * @param null $code
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function registerCustomerByPhone($phone, $code = null)
    {
        $customerCollection = $this->customerCollection->create();
        if ($code == null) {
            $customerCollection->addAttributeToSelect('*')
                           ->addAttributeToFilter('customer_mobile', ['eq' => $phone])
                           ->load();
        } else {
            $customerCollection->addAttributeToSelect('*')
                           ->addAttributeToFilter('customer_mobile', ['eq' => $phone])
                           ->addAttributeToFilter('countryreg', ['eq' => $code])
                           ->load();
        }

        if ($customerCollection->getSize() == 0) {
            return false;
        }
        return true;
    }

    /**
     * @param $phone
     * @param $customer
     * @param $otp
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function callEmailSending($phone, $customer, $otp, $email = null)
    {
        $minutes = $this->datahelper->getSmsProfileOTPExpiry();
        if ($customer == null) {
            $customerCollection = $this->customerCollection->create();
            $customerCollection->addAttributeToSelect('*');
            if ($email!=null) {
                $customerCollection->addAttributeToFilter('email', ['eq' => $email])
                ->load();
            } else {
                $customerCollection->addAttributeToFilter('customer_mobile', ['eq' => $phone])
                ->load();
            }
                

            $customer = $customerCollection->getLastItem();
        }

        if ($customer->getEmail() != null) {
            $storeDomain = $this->datahelper->getStoreDomain();
            if (strpos($customer->getEmail(), $storeDomain) !== false) {
                if (substr($customer->getEmail(), 0, 1)=="+") {
                    return $this;
                }
            }
        }
        
        $variables = [
            'customer_name' => $customer->getFirstname() . ' ' . $customer->getLastname(),
            'otp' => $otp,
            'date' => date("Y-m-d h:i:s"),
            'otp_expiry' => $minutes
        ];

        $sender = [
            'name' => "OTP to Login",
            'email' => $this->scopeConfig->getValue(
                'trans_email/ident_general/email',
                ScopeInterface::SCOPE_STORE
            )
        ];

        $this->inlineTranslation->suspend();
        $templateId = $this->datahelper->getSMSSendOtp();
        $this->transportBuilder->setTemplateIdentifier(
            $templateId
        )->setTemplateOptions(
            [
                'area' => Area::AREA_FRONTEND,
                'store' => $this->storeManager->getStore()->getId()
            ]
        )->setTemplateVars(
            $variables
        )->setFromByScope(
            $sender
        )->addTo(
            $customer->getEmail()
        );

        $transport = $this->transportBuilder->getTransport();

        try {
            $transport->sendMessage();
        } catch (\Exception $exception) {
        }
        $this->inlineTranslation->resume();

        return $this;
    }

    /**
     * @return mixed
     */
    private function getCurrentDate()
    {
        $_data =  $this->getRequest()->getParams();
        return $_data['mobile'];
    }
}
