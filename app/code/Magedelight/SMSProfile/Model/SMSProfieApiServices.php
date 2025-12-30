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

namespace Magedelight\SMSProfile\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\MailException;
use Magedelight\SMSProfile\Helper\Data as HelperData;
use Magedelight\SMSProfile\Model\SMSProfileOtpFactory;
use Magedelight\SMSProfile\Model\SMSProfileService;
use Magedelight\SMSProfile\Api\SMSProfileTemplatesRepositoryInterface;
use Magento\Framework\Controller\Result\JsonFactory as ResultJsonFactory;
use Magedelight\SMSProfile\Api\SMSProfieApiServicesInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magedelight\SMSProfile\Model\SMSProfileApiHelper as ApiHelper;
use Magento\Integration\Model\Oauth\TokenFactory as TokenModelFactory;
use Magento\Integration\Model\Oauth\Token\RequestThrottler;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Customer\Model\Customer\CredentialsValidator;
use Magento\Customer\Model\AccountManagement;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Math\Random;
use Magento\Checkout\Api\GuestPaymentInformationManagementInterface;
use Magento\Quote\Api\GuestCartManagementInterface;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface as PsrLogger;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Area;

class SMSProfieApiServices implements SMSProfieApiServicesInterface
{
        /**  @var HelperData */
    private $datahelper;

    /**  @var ApiHelper */
    private $apiHelper;

     /** @var SMSProfileService */
    private $smsProfileService;

    /** @var SMSProfileTemplatesRepositoryInterface */
    private $smsProfileTemplates;

    /**
     * sms profile otp  factory
     *
     * @var SMSProfileOtpFactory
     */
    private $smsProfileOtp;

    /** @var ResultJsonFactory */
    private $resultJsonFactory;

    /**
     * @var JsonHelper
     */
    private $jsonHelper;

    /**
     * @var RequestThrottler
     */
    private $requestThrottler;

     /**
      * @var CredentialsValidator
      */
    private $credentialsValidator;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Random
     */
    private $mathRandom;

    /**  @var TimezoneInterface */
    private $timezone;

    protected $date;
    protected $logger;
    protected $customerRepository;
    protected $tokenModelFactory;
    protected $guestPayment;
    protected $cartManagement;
    protected $customerCartManagement;
    protected $connectionPull;
    protected $storeManager;
    protected $customerCollection;
    protected $inlineTranslation;
    protected $transportBuilder;

    /**
     *  Initialize service
     * @param Context $context
     * @param SMSProfileService $smsProfileService
     * @param SMSProfileOtpFactory $smsProfileOtp
     * @param ResultJsonFactory $resultJsonFactory
     * @param SMSProfileTemplatesRepositoryInterface $smsProfileTemplates
     * @param HelperData $dataHelper
     */


    public function __construct(
        SMSProfileService $smsProfileService,
        SMSProfileOtpFactory $smsProfileOtp,
        ResultJsonFactory $resultJsonFactory,
        JsonHelper $jsonHelper,
        ApiHelper $apiHelper,
        SMSProfileTemplatesRepositoryInterface $smsProfileTemplates,
        HelperData $dataHelper,
        Random $mathRandom,
        GuestPaymentInformationManagementInterface $guestPayment,
        CredentialsValidator $credentialsValidator = null,
        CustomerRepositoryInterface $customerRepository,
        TokenModelFactory $tokenModelFactory,
        GuestCartManagementInterface $cartManagement,
        CartManagementInterface $customerCartManagement,
        PsrLogger $logger,
        TimezoneInterface $timezone,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollection,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        ResourceConnection $connectionPull = null
    ) {
        $this->datahelper = $dataHelper;
        $this->apiHelper = $apiHelper;
        $this->timezone = $timezone;
        $this->smsProfileService = $smsProfileService;
        $this->smsProfileTemplates = $smsProfileTemplates;
        $this->smsProfileOtp = $smsProfileOtp;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->jsonHelper = $jsonHelper;
        $this->customerRepository = $customerRepository;
        $this->tokenModelFactory = $tokenModelFactory;
        $this->mathRandom = $mathRandom;
        $this->guestPayment = $guestPayment;
        $this->cartManagement = $cartManagement;
        $this->customerCartManagement = $customerCartManagement;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->customerCollection = $customerCollection;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $logger;
        $this->date = $date;
        $this->credentialsValidator =
            $credentialsValidator ?: ObjectManager::getInstance()->get(CredentialsValidator::class);
        $this->connectionPull = $connectionPull ?: ObjectManager::getInstance()->get(ResourceConnection::class);
    }

    public function SendOtpToCustomer($resend, $storeId, $mobile, $eventType)
    {
        if ($this->datahelper->getModuleStatus()) {
            //$this->apiHelper->validateNumber($mobile);
            $otp = $this->datahelper->generateOTP();
            if (in_array($eventType, ['customer_account_edit_otp','customer_signup_otp','cod_otp']) && !is_numeric($mobile)) {
                throw new NoSuchEntityException(__('Please enter valid mobile number '));
            }
            if (in_array($eventType, ['customer_account_edit_otp','customer_signup_otp']) && is_numeric($mobile)) {
                $customerCollection = $this->customerCollection->create();
                $customerCollection->addAttributeToSelect('*');
                $customerCollection->addAttributeToFilter('customer_mobile', ['eq' => $mobile]);
                $customerCollection->load();
                $customer = $customerCollection->getLastItem();
                if ($customer->getEmail() !== null) {
                    throw new NoSuchEntityException(__('Account with this number already exist'));
                }
            }
            if (is_numeric($mobile)) {
                $sms = $this->smsProfileTemplates->getByEventType($eventType, $storeId);
                $keywords   = ['{otpCode}'];
                $message =str_replace($keywords, $otp, $sms->getData('template_content'));
                $result = [];
                $this->smsProfileService->setToNumber($mobile);
                $this->smsProfileService->setMessageContent($message);
                $this->smsProfileService->setTransactionType($eventType);
                $this->smsProfileService->setApiVersion($this->datahelper->getSmsProfileApiGateWay());
            }
            try {
                if (is_numeric($mobile)) {
                    if ($this->datahelper->getSmsProfileApiGateWay() == 'Twilio Api Service') {
                        $this->smsProfileService->sendOTPSmsWithTwilio();
                    } else {
                        if ($this->datahelper->getApiReauestResponseXML()) {
                            $this->smsProfileService->sendSmsProfileOTPViaOtherServicesXML();
                        } else {
                            $this->smsProfileService->sendSmsProfileOTPViaOtherServices();
                        }
                    }
                }

                if (!in_array($eventType, ['customer_signup_otp'])) {
                    if (!is_numeric($mobile)) {
                        $this->callEmailSending($mobile, '', $otp);
                    } elseif (in_array($this->datahelper->getSendOtpVia(), ['email', 'both']) && is_numeric($mobile)) {
                        $this->callEmailSending($mobile, '', $otp);
                    }
                }

                $currentDateTime = $this->date->gmtDate();

                $smsProfileOtp =  $this->smsProfileOtp->create();
                $smsProfileOtp->setOtpCode($otp);
                $smsProfileOtp->setCustomerMobile($mobile);
                $smsProfileOtp->setCreatedAt($currentDateTime);
                $smsProfileOtp->setUpdatedAt($currentDateTime);
                $smsProfileOtp->setResend($resend);
                $smsProfileOtp->save();
                $result['Success'] = __('success')->getText();
            } catch (Exception $e) {
                throw new NoSuchEntityException($e->getMessage());
            }
            return $this->jsonHelper->jsonEncode($result);
        } else {
             throw new NoSuchEntityException(__('Extension is disabled '));
        }
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
    private function callEmailSending($phone, $customer, $otp)
    {
        $minutes = $this->datahelper->getSmsProfileOTPExpiry();
        if ($customer == null) {
            $customerCollection = $this->customerCollection->create();
            $customerCollection->addAttributeToSelect('*');
            if (is_numeric($phone)) {
                $customerCollection->addAttributeToFilter('customer_mobile', ['eq' => $phone]);
            } else {
                $customerCollection->addAttributeToFilter('email', ['eq' => $phone]);
            }

            $customerCollection->load();
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

        if ($customer->getEmail() !== null) {
            $variables = [
                'customer_name' => $customer->getFirstname() . ' ' . $customer->getLastname(),
                'otp' => $otp,
                'date' => date("Y-m-d h:i:s"),
                'otp_expiry' => $minutes
            ];
    
            $sender = [
                'name' => $this->scopeConfig->getValue(
                    'trans_email/ident_general/name',
                    ScopeInterface::SCOPE_STORE
                ),
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
        } else {
            throw new NoSuchEntityException(__('Account doesn\'t exist'));
        }

        return $this;
    }

    public function createCustomerTokenWithOtp($mobile, $otp, $websiteId)
    {
        if ($this->datahelper->getModuleStatus()) {
            if ($this->datahelper->getSmsProfileOtpOnLogin() == 'login_otp') {
                //$this->apiHelper->validateNumber($mobile);
                $validateOtp = $this->apiHelper->VerifyOtpForToken($mobile, $otp);
                if ($validateOtp == __('Verified')->getText()) {
                    if (is_numeric($mobile)) {
                        $email = $this->apiHelper->getCustomerEmail($mobile);
                    } else {
                        $email = $mobile;
                    }
                    
                    $this->getRequestThrottler()->throttle($email, RequestThrottler::USER_TYPE_CUSTOMER);
                    try {
                        $customerDataObject = $this->apiHelper->authenticateCustomerByMail($email, $websiteId);
                    } catch (\Exception $e) {
                        $this->getRequestThrottler()->logAuthenticationFailure($email, RequestThrottler::USER_TYPE_CUSTOMER);
                        throw new AuthenticationException(
                            __('You did not sign in correctly or your account is temporarily disabled.')
                        );
                    }
                    $this->getRequestThrottler()->resetAuthenticationFailuresCount($email, RequestThrottler::USER_TYPE_CUSTOMER);
                    return $this->tokenModelFactory->create()->createCustomerToken($customerDataObject->getId())->getToken();
                } else {
                    throw new AuthenticationException(
                        __('Please login with valid OTP.')
                    );
                }
            } else {
                throw new AuthenticationException(
                    __('Please select properlogin option in admin configuration.')
                );
            }
        } else {
             throw new NoSuchEntityException(__('Extension is disabled '));
        }
    }

    public function createCustomerTokenWithOtpPassword($mobile, $otp, $password, $websiteId)
    {
        if ($this->datahelper->getModuleStatus()) {
            if ($this->datahelper->getSmsProfileOtpOnLogin() == 'login_both') {
               //$this->apiHelper->validateNumber($mobile);
                $validateOtp = $this->apiHelper->VerifyOtpForToken($mobile, $otp);
                if ($validateOtp == __('Verified')->getText()) {
                    if (is_numeric($mobile)) {
                        $email = $this->apiHelper->getCustomerEmail($mobile);
                    } else {
                        $email=$mobile;
                    }
                    
                    $this->getRequestThrottler()->throttle($email, RequestThrottler::USER_TYPE_CUSTOMER);
                    try {
                        $customerDataObject = $this->apiHelper->authenticateCustomer($email, $password, $websiteId);
                    } catch (\Exception $e) {
                        $this->getRequestThrottler()->logAuthenticationFailure($email, RequestThrottler::USER_TYPE_CUSTOMER);
                        throw new AuthenticationException(
                            __('You did not sign in correctly or your account is temporarily disabled.')
                        );
                    }
                        $this->getRequestThrottler()->resetAuthenticationFailuresCount($email, RequestThrottler::USER_TYPE_CUSTOMER);
                        return $this->tokenModelFactory->create()->createCustomerToken($customerDataObject->getId())->getToken();
                }
            } else {
                return 'please select properlogin option in admin configuration.';
            }
        } else {
             throw new NoSuchEntityException(__('Extension is disabled '));
        }
    }

    public function createAccountWithOtp(CustomerInterface $customer, $mobile, $otp, $password = null, $redirectUrl = '')
    {
        if ($this->datahelper->getModuleStatus()) {
            //$this->apiHelper->validateNumber($mobile);
            $validateOtp = $this->apiHelper->VerifyOtpForToken($mobile, $otp);
            if ($validateOtp == __('Verified')->getText()) {
                if ($password !== null) {
                    $this->apiHelper->checkPasswordStrength($password);
                    $customerEmail = $customer->getEmail();
                    try {
                        $this->credentialsValidator->checkPasswordDifferentFromEmail($customerEmail, $password);
                    } catch (InputException $e) {
                        throw new LocalizedException(__('Password cannot be the same as email address.'));
                    }
                    $hash = $this->apiHelper->createPasswordHash($password);
                } else {
                    $hash = null;
                }
                $customer->setCustomAttribute('customer_mobile', $mobile);
                return $this->apiHelper->createAccountWithPasswordHash($customer, $hash, $redirectUrl);
            } else {
                throw new LocalizedException(__('OTP not verified.'));
            }
        } else {
             throw new NoSuchEntityException(__('Extension is disabled '));
        }
    }

    public function updateAccountWithOtp(CustomerInterface $customer, $mobile, $otp, $websiteId, $password = null, $redirectUrl = '')
    {
        if ($this->datahelper->getModuleStatus()) {
           // $this->apiHelper->validateNumber($mobile);
            $validateOtp = $this->apiHelper->VerifyOtpForToken($mobile, $otp);
            if ($validateOtp == __('Verified')->getText()) {
                $this->customerRepository->save($customer);
                $customer = $this->customerRepository->get($customer->getEmail(), $websiteId);
                return $customer;
            }
        } else {
             throw new NoSuchEntityException(__('Extension is disabled '));
        }
    }

    public function initiatePasswordResetWithOTP($mobile, $otp, $template, $websiteId)
    {
        if ($this->datahelper->getModuleStatus()) {
            //$this->apiHelper->validateNumber($mobile);
            $validateOtp = $this->apiHelper->VerifyOtpForToken($mobile, $otp);
            if ($validateOtp == __('Verified')->getText()) {
                if (is_numeric($mobile)) {
                    $email = $this->apiHelper->getCustomerEmail($mobile);
                } else {
                    $email=$mobile;
                }
                
                // load customer by email
                $customer = $this->customerRepository->get($email, $websiteId);
                $newPasswordToken = $this->mathRandom->getUniqueHash();
                $this->apiHelper->changeResetPasswordLinkToken($customer, $newPasswordToken);
                try {
                    switch ($template) {
                        case AccountManagement::EMAIL_REMINDER:
                            $this->apiHelper->getEmailNotification()->passwordReminder($customer);
                            break;
                        case AccountManagement::EMAIL_RESET:
                            $this->apiHelper->getEmailNotification()->passwordResetConfirmation($customer);
                            break;
                        default:
                            throw new InputException(__(
                                'Invalid value of "%value" provided for the %fieldName field. '.
                                'Possible values: %template1 or %template2.',
                                [
                                    'value' => $template,
                                    'fieldName' => 'template',
                                    'template1' => AccountManagement::EMAIL_REMINDER,
                                    'template2' => AccountManagement::EMAIL_RESET
                                ]
                            ));
                    }

                    return true;
                } catch (MailException $e) {
                    // If we are not able to send a reset password email, this should be ignored
                    $this->logger->critical($e);
                }
            }
            return false;
        } else {
             throw new NoSuchEntityException(__('Extension is disabled '));
        }
    }

    public function savePaymentInformationAndPlaceOrderWithOtp(
        $cartId,
        $email,
        $mobile,
        $otp,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        if ($this->datahelper->getModuleStatus()) {
            //$this->apiHelper->validateNumber($mobile);
            $validateOtp = $this->apiHelper->VerifyOtpForToken($mobile, $otp);
            if ($validateOtp == __('Verified')->getText()) {
                $salesConnection = $this->connectionPull->getConnection('sales');
                $checkoutConnection = $this->connectionPull->getConnection('checkout');
                $salesConnection->beginTransaction();
                $checkoutConnection->beginTransaction();

                try {
                    $this->apiHelper->savePaymentInformation($cartId, $email, $paymentMethod, $billingAddress);
                    try {
                        $orderId = $this->cartManagement->placeOrder($cartId);
                    } catch (\Magento\Framework\Exception\LocalizedException $e) {
                        throw new CouldNotSaveException(
                            __($e->getMessage()),
                            $e
                        );
                    } catch (\Exception $e) {
                        $this->logger->critical($e);
                        throw new CouldNotSaveException(
                            __('An error occurred on the server. Please try to place the order again.'),
                            $e
                        );
                    }
                    $salesConnection->commit();
                    $checkoutConnection->commit();
                } catch (\Exception $e) {
                    $salesConnection->rollBack();
                    $checkoutConnection->rollBack();
                    throw $e;
                }
                
                return $orderId;
            }
        } else {
             throw new NoSuchEntityException(__('Extension is disabled '));
        }
    }

    public function savePaymentInformationAndPlaceOrderWithOtpForUser(
        $cartId,
        $mobile,
        $otp,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        if ($this->datahelper->getModuleStatus()) {
            //$this->apiHelper->validateNumber($mobile);
            $validateOtp = $this->apiHelper->VerifyOtpForToken($mobile, $otp);
            if ($validateOtp == __('Verified')->getText()) {
                $this->apiHelper->savePaymentInformationforUser($cartId, $paymentMethod, $billingAddress);
                try {
                    $orderId = $this->customerCartManagement->placeOrder($cartId);
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    throw new CouldNotSaveException(
                        __($e->getMessage()),
                        $e
                    );
                } catch (\Exception $e) {
                     $this->logger->critical($e);
                    throw new CouldNotSaveException(
                        __('An error occurred on the server. Please try to place the order again.'),
                        $e
                    );
                }
                return $orderId;
            }
        } else {
             throw new NoSuchEntityException(__('Extension is disabled '));
        }
    }

    /**
     * Get request throttler instance
     *
     * @return RequestThrottler
     * @deprecated 100.0.4
     */
    private function getRequestThrottler()
    {
        if (!$this->requestThrottler instanceof RequestThrottler) {
            return \Magento\Framework\App\ObjectManager::getInstance()->get(RequestThrottler::class);
        }
        return $this->requestThrottler;
    }
}
