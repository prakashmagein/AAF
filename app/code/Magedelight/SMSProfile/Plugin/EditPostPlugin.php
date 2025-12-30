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

use Magedelight\SMSProfile\Helper\Data as HelperData;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magedelight\SMSProfile\Model\ResourceModel\SMSProfileOtp\CollectionFactory;
use Magedelight\SMSProfile\Model\ResourceModel\SMSProfileOtpAttempt\CollectionFactory as AttemptCollectionFactory;

class EditPostPlugin
{

    

    /**  @var HelperData */
    private $datahelper;

    /**  @var Session */
    private $session;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    private $messageManager;

    /**
     * @var AttemptCollectionFactory
     */
    private $otpcollection;

    /**
     * @var AttemptCollectionFactory
     */
    private $attemptcollection;

    /**
     * @var TimezoneInterface
     */
    private $timezone;


    /**
     * Constructor
     * @param HelperData $dataHelper
     * @param Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param ManagerInterface $messageManager
     * @param TimezoneInterface $timezone
     * @param AttemptCollectionFactory $otpcollection
     */

    public function __construct(
        HelperData $dataHelper,
        Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        ManagerInterface $messageManager,
        TimezoneInterface $timezone,
        CollectionFactory $otpcollection,
        AttemptCollectionFactory $attemptcollection
    ) {
        $this->datahelper = $dataHelper;
        $this->session = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->messageManager = $messageManager;
        $this->timezone = $timezone;
        $this->otpcollection = $otpcollection;
        $this->attemptcollection = $attemptcollection;
    }

    public function beforeExecute(\Magento\Customer\Controller\Account\EditPost $subject)
    {

        if ($this->datahelper->getModuleStatus()) {
            $post = $subject->getRequest()->getPost();
            //get current customer mobile number
            $customer = $this->getCustomerDataObject($this->session->getCustomerId());
            $customerMobileNumber = "";
            if ($customer->getCustomAttribute('customer_mobile')) {
                $customerMobileNumber = $customer->getCustomAttribute('customer_mobile')->getValue();
            }
            
            if (($customerMobileNumber=="" && isset($post['customer_mobile'])) || (isset($post['customer_mobile']) && $customerMobileNumber!=$post['customer_mobile'])) {
                //check otp verified
                $isOtpVerified = 0;

                if ($post['otp']!="") {
                    $otp = $post['otp'];
                    $toNumber = $post['customer_mobile'];
                    if ($this->datahelper->isCustomerCountryEnabled() && isset($post['countryreg'])) {
                        $toNumber = $post['countryreg'].$post['customer_mobile'];
                    }
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
                            $data->delete();
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
                            $message = __('OTP is not valid.');
                        }
                    } else {
                        $message = __('OTP is expired.');
                    }
                } else {
                    $message = __('Please verify OTP.');
                }

                if ($isOtpVerified==0) {
                    $this->messageManager->addError($message);
                    $subject->getRequest()->setParam('form_key', '');
                }
            } else {
                if (isset($post['customer_mobile']) && !$customerMobileNumber==$post['customer_mobile']) {
                    $message = __('Please verify OTP.');
                    $this->messageManager->addError($message);
                    $subject->getRequest()->setParam('form_key', '');
                }
            }
        }
    }

    /**
     * Get customer data object
     *
     * @param int $customerId
     *
     * @return CustomerInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function getCustomerDataObject($customerId)
    {
        return $this->customerRepository->getById($customerId);
    }
}
