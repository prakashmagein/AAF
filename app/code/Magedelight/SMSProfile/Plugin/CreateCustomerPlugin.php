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

use Magedelight\SMSProfile\Model\SMSNotificationService;
use Magedelight\SMSProfile\Helper\Data as HelperData;
use Magento\Store\Model\StoreManagerInterface;
use Magedelight\SMSProfile\Api\SMSTemplatesRepositoryInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class CreateCustomerPlugin
{

    /**  @var SMSNotificationService */
    private $smsNotificationService;

    /**  @var HelperData */
    private $datahelper;

    /**  @var StoreManagerInterface */
    private $storeManager;

    /**  @var SMSTemplatesRepositoryInterface */
    private $smsTemplatesRepository;

    private $_messageManager;

    /**  @var \Magento\Framework\App\RequestInterface */
    private $_request;

    /**  @var TimezoneInterface */
    private $timezone;

    /**  @var \Magedelight\SMSProfile\Model\ResourceModel\SMSProfileOtp\CollectionFactory */
    private $otpcollection;

    /**  @var \Magedelight\SMSProfile\Model\ResourceModel\SMSProfileOtpAttempt\CollectionFactory */
    private $attemptcollection;

    /**
     * Constructor
     * @param SMSNotificationService $smsNotificationService
     * @param SMSTemplatesRepositoryInterface  $smsTemplatesRepository
     * @param StoreManagerInterface $storeManager
     * @param HelperData $dataHelper
     */

    public function __construct(
        SMSNotificationService $smsNotificationService,
        SMSTemplatesRepositoryInterface $smsTemplatesRepository,
        StoreManagerInterface $storeManager,
        HelperData $dataHelper,
        \Magento\Framework\App\RequestInterface $request,
        TimezoneInterface $timezone,
        \Magedelight\SMSProfile\Model\ResourceModel\SMSProfileOtp\CollectionFactory $otpcollection,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magedelight\SMSProfile\Model\ResourceModel\SMSProfileOtpAttempt\CollectionFactory $attemptcollection
    ) {
        $this->smsNotificationService = $smsNotificationService;
        
        $this->datahelper = $dataHelper;
        $this->storeManager = $storeManager;
        $this->smsTemplatesRepository  = $smsTemplatesRepository;
        $this->_request = $request;
        $this->timezone = $timezone;
        $this->otpcollection = $otpcollection;
        $this->_messageManager = $messageManager;
        $this->attemptcollection = $attemptcollection;
    }

    public function beforeExecute(\Magento\Customer\Controller\Account\CreatePost $subject)
    {
        
        if (!$this->datahelper->getModuleStatus() || !$this->datahelper->getSmsProfilePhoneRequiredOnSignUp()) {
            return;
        }

        $postData = $this->_request->getPost();
       
        //Verify OTP :
        $isOtpVerified=0;
        $message='';

        $toNumber = $postData['customer_mobile'];
        if ($this->datahelper->isCustomerCountryEnabled() && isset($postData['countryreg'])) {
            $toNumber = $postData['countryreg'].$postData['customer_mobile'];
        }
        if (in_array($this->datahelper->getSendOtpVia(), ['sms', 'both'])) {
            if (isset($postData['otp'])) {
                $otp = $postData['otp'];
    
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
                    }
                    $message = __('OTP is not valid');
                } else {
                    $message = __('Please verify otp');
                }
            } else {
                $message = __('Please verify otp');
            }

            if ($isOtpVerified==0) {
                $this->_messageManager->addError($message);
                $subject->getRequest()->setParam('form_key', '');
            }
        }
    }
}
