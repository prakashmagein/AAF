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

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ValidateSmsProfileConfig implements ObserverInterface
{
    /** @var RequestInterface  */
    private $request;

    /** @var \Magento\Framework\Message\ManagerInterface $messageManager  */
    private $messageManager;
    /** @var WriterInterface  */
    private $configWriter;


    /**
     * ConfigChange constructor.
     * @param RequestInterface $request
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param WriterInterface $configWriter
     */
    public function __construct(
        RequestInterface $request,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        WriterInterface $configWriter
    ) {
        $this->request = $request;
        $this->messageManager = $messageManager;
        $this->configWriter = $configWriter;
    }

    /**
     * @param EventObserver $observer
     * @return $this|void
     */
    public function execute(EventObserver $observer)
    {

        $groupParams = $this->request->getParam('groups');

        $fields = $groupParams['general']['fields'];

        $sendOtp = isset($fields['send_otp_via']['value'])?$fields['send_otp_via']['value']:'email';

        $fields = $groupParams['otpsetting']['fields'];

        if ($sendOtp=='email') {
            if (isset($fields['required_phone']['value']) && $fields['required_phone']['value']==1) {
                $this->messageManager->addWarning(__("Mobile number field will not show at register page as Send OTP via set as email."));
                if (isset($fields['required_email']['value']) && $fields['required_email']['value']==1) {
                    $this->setData('magedelightsmsprofile/otpsetting/required_email', 0);
                    throw new \Magento\Framework\Exception\LocalizedException(__("Mobile or Email field required on register page."));
                }
            }
        }

        if (isset($fields['required_phone']['value']) && $fields['required_phone']['value']==0) {
            if (isset($fields['required_email']['value']) && $fields['required_email']['value']==1) {
                $this->setData('magedelightsmsprofile/otpsetting/required_email', 0);
                throw new \Magento\Framework\Exception\LocalizedException(__("Mobile or Email field required on register page."));
            }
        }


        if (isset($fields['required_email']['value']) && $fields['required_email']['value']==1) {
            $this->setData('customer/create_account/confirm', 0);
        }

        return $this;
    }

    /**
     * @param $path
     * @param $value
     */
    public function setData($path, $value)
    {
        $this->configWriter->save(
            $path,
            $value,
            $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $scopeId = 0
        );
    }
}
