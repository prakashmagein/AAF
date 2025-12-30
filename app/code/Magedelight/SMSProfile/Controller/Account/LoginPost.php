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
 
namespace Magedelight\SMSProfile\Controller\Account;

use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Customer\Model\Customer;
use Magedelight\SMSProfile\Helper\Data as HelperData;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class LoginPost extends \Magento\Customer\Controller\Account\LoginPost
{
    private $customerUrl;
    /** @var CollectionFactory  */
    private $otpcollection;
    /** @var CollectionFactory  */
    private $attemptcollection;
    /** @var TimezoneInterface */
    private $timezone;
    /** @var CollectionFactory */
    private $customerCollection;
    /** @var Customer */
    private $customer;
    /** @var StoreManagerInterface */
    private $store;
    /** @var HelperData */
    private $datahelper;

    public function __construct(
        Context $context,
        Customer $customer,
        HelperData $datahelper,
        StoreManagerInterface $store,
        Session $customerSession,
        AccountManagementInterface $customerAccountManagement,
        CustomerUrl $customerHelperData,
        Validator $formKeyValidator,
        AccountRedirect $accountRedirect,
        CollectionFactory $customerCollection,
        TimezoneInterface $timezone,
        \Magedelight\SMSProfile\Model\ResourceModel\SMSProfileOtp\CollectionFactory $otpcollection,
        \Magedelight\SMSProfile\Model\ResourceModel\SMSProfileOtpAttempt\CollectionFactory $attemptcollection
    ) {
        $this->customerCollection = $customerCollection;
        $this->customer = $customer;
        $this->store = $store;
        $this->datahelper = $datahelper;
        $this->customerUrl = $customerHelperData;
        $this->timezone = $timezone;
        $this->otpcollection = $otpcollection;
        $this->attemptcollection = $attemptcollection;

        parent::__construct($context, $customerSession, $customerAccountManagement, $customerHelperData, $formKeyValidator, $accountRedirect);
    }

    /**
     * Login post action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        if (!$this->datahelper->getModuleStatus()) {
            return parent::execute();
        }
        
        if ($this->session->isLoggedIn() || !$this->formKeyValidator->validate($this->getRequest())) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }

        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');

            if (isset($login['password']) && !empty($login['password']) && isset($login['username']) && !empty($login['username'])) {
                $login['mobile']=null;
            }

            if (isset($login['mobile']) && !empty($login['mobile']) && $login['otp'] != 1) {
                 $message = __(
                     'Please verify OTP.'
                 );
                $this->messageManager->addError($message);
                $this->session->setUsername(@$login['username']);
                return $this->accountRedirect->getRedirect();
            }
            $isOtpVerified=0;
            if (isset($login['mobile']) && $login['mobile']!="") {
                $email ='';
                if (is_numeric($login['mobile'])) {
                    $customerCollections = $this->getCustomerByPhone($login['mobile']);
                    foreach ($customerCollections as $customer) {
                        $email = $customer->getEmail();
                    }
                    if ($email == '') {
                        $message = __(
                            'Account with this number doesn\'t exist'
                        );
                        $this->messageManager->addErrorMessage($message);
                        $this->session->setUsername($login['mobile']);
                        return $this->accountRedirect->getRedirect();
                    }
                } else {
                    $customerCollections = $this->getCustomerByEmail($login['mobile']);
                    foreach ($customerCollections as $customer) {
                        $email = $customer->getEmail();
                    }
                    if ($email == '') {
                        $message = __(
                            'Account with this number doesn\'t exist'
                        );
                        $this->messageManager->addErrorMessage($message);
                        $this->session->setUsername($login['mobile']);
                        return $this->accountRedirect->getRedirect();
                    }
                }
                $login['username'] = $email;

                //Verify OTP :
                $isOtpVerified=0;
                    $toNumber = $login['mobile'];
                if ($this->datahelper->isCustomerCountryEnabled() && isset($login['countrycodeval']) && is_numeric($login['mobile'])) {
                    $toNumber = $login['countrycodeval'].$login['mobile'];
                }
                   
                    $otp = $login['otp_val'];
                    $minutes = $this->datahelper->getSmsProfileOTPExpiry();
                    $now = $this->timezone->date(null, null, false)->format('Y-m-d H:i:s');
                    $now2 = $this->timezone->date(null, null, false)->modify('-' . $minutes . 'minute')->format('Y-m-d H:i:s');
                  
                    $smsProfileOtp = $this->otpcollection->create();
                    $smsProfileOtp->addFieldToFilter('customer_mobile', $toNumber);
                    $smsProfileOtp->addFieldToFilter('created_at', ['from' => $now2, 'to' => $now]);
                    $smsProfileOtp->addFieldToFilter('created_at', ['gteq' => $now2, 'lteq' => $now]);
                    $smsProfileOtp->getSelect();
                   $smsProfileOtp->getLastItem();
                   $smsProfileOtp->getSize();
                if ($smsProfileOtp->getSize()) {
                    $data = $smsProfileOtp->getLastItem();
                    if ($data->getOtpCode() == $otp) {
                        $data->delete();
                        $isOtpVerified=1;
                        //Reset customer Attempt Data:
                        $smsProfileattempt = $this->attemptcollection->create();
                        $smsProfileattempt->addFieldToFilter('customer_mobile', $toNumber);
                        $adata = $smsProfileattempt->getLastItem();
                        $adata->setAttempCount(0);
                        $adata->setResendCountTime(null);
                        $adata->save();
                    } else {
                        $isOtpVerified=0;
                    }
                } else {
                    $message = __('OTP is expired.');
                }
            }

            if (is_numeric(@$login['username'])) {
                $email = "";
                $customerCollections = $this->getCustomerByPhone($login['username']);
                foreach ($customerCollections as $customer) {
                    $email = $customer->getEmail();
                }
                if ($email == '') {
                    $message = __('Account with this number doesn\'t exist');
                    $this->messageManager->addErrorMessage($message);
                    $this->session->setUsername($login['username']);
                    return $this->accountRedirect->getRedirect();
                }
                $login['username'] = $email;
            }
            
            if ($this->datahelper->getSmsProfileOtpOnLogin() == 'login_otp' && (!empty($login['username']) && ($login['otp'] == 1) && ($isOtpVerified == 1))) {
                try {
                    $this->customer->setWebsiteId($this->store->getStore()->getWebsiteId());
                    $customer = $this->customer->loadByEmail($login['username']);
                    $this->session->setCustomerAsLoggedIn($customer);
                    
                    $redirectUrl = $this->accountRedirect->getRedirectCookie();
                } catch (EmailNotConfirmedException $e) {
                    $value = $this->customerUrl->getEmailConfirmationUrl($login['username']);
                    $message = __(
                        'This account is not confirmed. <a href="%1">Click here</a> to resend confirmation email.',
                        $value
                    );
                } catch (UserLockedException $e) {
                    $message = __(
                        'You did not sign in correctly or your account is temporarily disabled.'
                    );
                } catch (AuthenticationException $e) {
                    $message = __('You did not sign in correctly or your account is temporarily disabled.');
                } catch (LocalizedException $e) {
                    $message = $e->getMessage();
                } catch (\Exception $e) {
                    // PA DSS violation: throwing or logging an exception here can disclose customer password
                    $this->messageManager->addError(
                        __('An unspecified error occurred. Please contact us for assistance.')
                    );
                } finally {
                    if (isset($message)) {
                        $this->messageManager->addError($message);
                        $this->session->setUsername($login['username']);
                    }
                }
            } elseif ($this->datahelper->getSmsProfileOtpOnLogin() == 'login_both' && !empty($login['username']) && isset($login['otp']) && $isOtpVerified == 1) {
                try {
                    $this->customer->setWebsiteId($this->store->getStore()->getWebsiteId());
                    $customer = $this->customer->loadByEmail($login['username']);
                    $this->session->setCustomerAsLoggedIn($customer);
                    
                    $redirectUrl = $this->accountRedirect->getRedirectCookie();
                } catch (EmailNotConfirmedException $e) {
                    $value = $this->customerUrl->getEmailConfirmationUrl($login['username']);
                    $message = __(
                        'This account is not confirmed. <a href="%1">Click here</a> to resend confirmation email.',
                        $value
                    );
                } catch (UserLockedException $e) {
                    $message = __(
                        'You did not sign in correctly or your account is temporarily disabled.'
                    );
                } catch (AuthenticationException $e) {
                    $message = __('You did not sign in correctly or your account is temporarily disabled.');
                } catch (LocalizedException $e) {
                    $message = $e->getMessage();
                } catch (\Exception $e) {
                    // PA DSS violation: throwing or logging an exception here can disclose customer password
                    $this->messageManager->addErrorMessage(
                        __('An unspecified error occurred. Please contact us for assistance.')
                    );
                } finally {
                    if (isset($message)) {
                        $this->messageManager->addError($message);
                        $this->session->setUsername($login['username']);
                    }
                }
            } elseif (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $customer = $this->customerAccountManagement->authenticate($login['username'], $login['password']);
                    $this->session->setCustomerDataAsLoggedIn($customer);
                    $this->session->regenerateId();
                    
                    $redirectUrl = $this->accountRedirect->getRedirectCookie();
                } catch (EmailNotConfirmedException $e) {
                    $value = $this->customerUrl->getEmailConfirmationUrl($login['username']);
                    $message = __(
                        'This account is not confirmed. <a href="%1">Click here</a> to resend confirmation email.',
                        $value
                    );
                } catch (UserLockedException $e) {
                    $message = __(
                        'You did not sign in correctly or your account is temporarily disabled.'
                    );
                } catch (AuthenticationException $e) {
                    $message = __('You did not sign in correctly or your account is temporarily disabled.');
                } catch (LocalizedException $e) {
                    $message = $e->getMessage();
                } catch (\Exception $e) {
                    // PA DSS violation: throwing or logging an exception here can disclose customer password
                    $this->messageManager->addErrorMessage(
                        __('An unspecified error occurred. Please contact us for assistance.')
                    );
                } finally {
                    if (isset($message)) {
                        $this->messageManager->addErrorMessage($message);
                        $this->session->setUsername($login['username']);
                    }
                }
            } else {
                $this->messageManager->addErrorMessage(__('A login and a password are required.'));
            }

            if (!(is_numeric(@$login['username'])) && $this->session->isLoggedIn() && !empty(@$login['username']) && $this->datahelper->redirectOldCustomer()) {
                if ($this->getCurrentCustomerPhone($login['username']) === null) {
                    $resultRedirect = $this->resultRedirectFactory->create();
                    $resultRedirect->setPath('customer/account/edit');
                    $this->messageManager->addNoticeMessage(__('Please enter mobile number.'));
                    return $resultRedirect;
                }
            }
        }

        return $this->accountRedirect->getRedirect();
    }

    public function getCustomerByPhone($phone)
    {
        $customerCollection = $this->customerCollection->create();
        $customerCollection->addAttributeToSelect('*')
                           ->addAttributeToFilter('customer_mobile', $phone)
                           ->load();
        return $customerCollection;
    }

    public function getCustomerByEmail($phone)
    {
        $customerCollection = $this->customerCollection->create();
        $customerCollection->addAttributeToSelect('*')
                           ->addAttributeToFilter('email', $phone)
                           ->load();
        return $customerCollection;
    }


    public function getCurrentCustomerPhone($email)
    {
        $tel = '';
        $customerCollection = $this->customerCollection->create();
        $customerCollection->addAttributeToSelect('*')
                           ->addFieldToFilter('email', $email)
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
