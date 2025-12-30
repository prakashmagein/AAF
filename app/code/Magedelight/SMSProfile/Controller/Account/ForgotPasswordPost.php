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

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\AccountManagement;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\SecurityViolationException;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\Encryption\Encryptor;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Store\Model\StoreManagerInterface;
use Magedelight\SMSProfile\Helper\Data as HelperData;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * ForgotPasswordPost controller
 * @codingStandardsIgnoreFile
 */
class ForgotPasswordPost extends \Magento\Customer\Controller\Account\ForgotPasswordPost
{
     /**
      * Encryptor.
      *
      * @var Encryptor
      */
    private $encryptor;

     /**
      * CustomerRepository.
      *
      * @var CustomerRepository
      */

    private $otpcollection;
    /** @var CollectionFactory  */
    private $attemptcollection;
    /** @var TimezoneInterface */
    private $timezone;
    private $datahelper;
    private $customerRepository;
    private $customerCollection;
    private $storeManager;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Session $customerSession
     * @param AccountManagementInterface $customerAccountManagement
     * @param CollectionFactory $customerCollection
     * @param Escaper $escaper
     * @param Encryptor $encryptor
     * @param CustomerRepository $customerRepository
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        AccountManagementInterface $customerAccountManagement,
        CollectionFactory $customerCollection,
        Escaper $escaper,
        Encryptor $encryptor,
        CustomerRepository $customerRepository,
        StoreManagerInterface $storeManager,
        TimezoneInterface $timezone,
        \Magedelight\SMSProfile\Model\ResourceModel\SMSProfileOtp\CollectionFactory $otpcollection,
        HelperData $datahelper,
        \Magedelight\SMSProfile\Model\ResourceModel\SMSProfileOtpAttempt\CollectionFactory $attemptcollection
    ) {
        $this->customerCollection = $customerCollection;
        $this->encryptor = $encryptor;
        $this->customerRepository = $customerRepository;
        $this->storeManager = $storeManager;
        $this->timezone = $timezone;
        $this->otpcollection = $otpcollection;
        $this->datahelper = $datahelper;
        $this->attemptcollection = $attemptcollection;
        parent::__construct($context, $customerSession, $customerAccountManagement, $escaper);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $emailAddress = (string)$this->getRequest()->getPost('email');
        $mobile = $this->getRequest()->getPost('customer_mobile');
        $forgetOtpVal = (string)$this->getRequest()->getPost('otp');
        $forgetOtpValCode = (string)$this->getRequest()->getPost('forgetOtpValCode');

        $isOtpVerified=0;
        if (isset($mobile) && !empty($mobile) && $this->getRequest()->getPost('forgetOtpValidation') !=1) {
            $message = __(
                'Please verify OTP.'
            );
                $this->session->setForgottenEmail($mobile);
                $this->messageManager->addErrorMessage($message);
                return $resultRedirect->setPath('*/*/forgotpassword');
        }

        if (isset($mobile) && !empty($mobile)) {
            $email = '';
            if(is_numeric($mobile)){
                $customerCollections = $this->getCustomerByPhone($mobile);
                foreach ($customerCollections as $customer) {
                    $email = $customer->getEmail();
                }
            }else{
                $customerCollections = $this->getCustomerByEmail($mobile);
                foreach ($customerCollections as $customer) {
                    $email = $customer->getEmail();
                }
            }
            if ($email == '') {
                $message = __(
                    'Account with this number doesn\'t exist'
                );
                $this->session->setForgottenEmail($email);
                $this->messageManager->addErrorMessage($message);
                return $resultRedirect->setPath('*/*/forgotpassword');
            }

            //Verify OTP :
           
            $toNumber = $mobile;
            if ($this->datahelper->isCustomerCountryEnabled() && isset($forgetOtpValCode) && is_numeric($mobile)) {
                $toNumber = $this->getRequest()->getPost('countryreg').$mobile;
            }
           
            $otp = $forgetOtpVal;
            $minutes = $this->datahelper->getSmsProfileOTPExpiry();
            $now = $this->timezone->date(null, null, false)->format('Y-m-d H:i:s');
            $now2 = $this->timezone->date(null, null, false)->modify('-' . $minutes . 'minute')->format('Y-m-d H:i:s');
          
            $smsProfileOtp = $this->otpcollection->create();
            $smsProfileOtp->addFieldToFilter('customer_mobile', $toNumber);
            $smsProfileOtp->addFieldToFilter('created_at', ['from' => $now2, 'to' => $now]);
            $smsProfileOtp->addFieldToFilter('created_at', ['gteq' => $now2, 'lteq' => $now]);
            $smsProfileOtp->getLastItem();
            $smsProfileOtp->getSize();
            if ($smsProfileOtp->getSize()) {
                $data = $smsProfileOtp->getLastItem();
                if ($data->getOtpCode() == $otp) {
                    //$data->delete();
                    //Reset customer Attempt Data:
                    $smsProfileattempt = $this->attemptcollection->create();
                    $smsProfileattempt->addFieldToFilter('customer_mobile', $toNumber);
                    $adata = $smsProfileattempt->getLastItem();
                    $adata->setAttempCount(0);
                    $adata->setResendCountTime(null);
                    $adata->save();
                    $isOtpVerified=1;
                } else {
                    $isOtpVerified=0;
                }
            } else {
                $message = __('OTP is expired.');
            }
        }
       
        if ($this->getRequest()->getPost('forgetOtpValidation') ==1 && $this->getRequest()->getPost('password') != null && $isOtpVerified == 1) {
            $password = $this->getRequest()->getPost('password');
            $passwordConfirmation = $this->getRequest()->getPost('password_confirmation');
            $passwordHash =$this->encryptor->getHash($password, true);
            $websiteId = $this->storeManager->getWebsite()->getId();

            try {
                $customer = $this->customerRepository->get($email, $websiteId);
                $this->customerRepository->save($customer, $passwordHash);
                $this->messageManager->addSuccessMessage(
                    __('You have successfully reset password.')
                );
                return $resultRedirect->setPath('*/*/login');
            } catch (\Exception $exception) {
                    $this->messageManager->addExceptionMessage(
                        $exception,
                        __('We\'re unable to send the password reset email.')
                    );
                    return $resultRedirect->setPath('*/*/forgotpassword');
            }

        } else {
            if ($emailAddress) {
                $customValidator = new \Magento\Framework\Validator\EmailAddress();
                $isValid = $customValidator->isValid($emailAddress);
                if (!$isValid) {
                    $this->session->setForgottenEmail($emailAddress);
                    $this->messageManager->addErrorMessage(__('Please correct the email address.'));
                    return $resultRedirect->setPath('*/*/forgotpassword');
                }

                try {
                    $this->customerAccountManagement->initiatePasswordReset(
                        $emailAddress,
                        AccountManagement::EMAIL_RESET
                    );
                } catch (NoSuchEntityException $exception) {
                    // Do nothing, we don't want anyone to use this action to determine which email accounts are registered.
                } catch (SecurityViolationException $exception) {
                    $this->messageManager->addErrorMessage($exception->getMessage());
                    return $resultRedirect->setPath('*/*/forgotpassword');
                } catch (\Exception $exception) {
                    $this->messageManager->addExceptionMessage(
                        $exception,
                        __('We\'re unable to send the password reset email.')
                    );
                    return $resultRedirect->setPath('*/*/forgotpassword');
                }
                $this->messageManager->addSuccessMessage($this->getSuccessMessage($emailAddress));
                return $resultRedirect->setPath('*/*/login');
            } else {

                if ($this->getRequest()->getPost('forgetOtpValidation') ==1 && $isOtpVerified == 0) {
                    $this->messageManager->addErrorMessage(__('Please verify OTP.'));
                } else {
                    $this->messageManager->addErrorMessage(__('Please enter your email.'));
                }
                return $resultRedirect->setPath('*/*/forgotpassword');
            }
        }
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
}
