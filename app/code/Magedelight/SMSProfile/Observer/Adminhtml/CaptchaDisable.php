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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class CaptchaDisable implements ObserverInterface
{
    /** @var RequestInterface  */
    private $request;

    /** @var WriterInterface  */
    private $configWriter;

    /** @var ScopeConfigInterface  */
    private $scopeConfig;

    /**
     * ConfigChange constructor.
     * @param RequestInterface $request
     * @param WriterInterface $configWriter
     */
    public function __construct(
        RequestInterface $request,
        WriterInterface $configWriter,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->request = $request;
        $this->configWriter = $configWriter;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param EventObserver $observer
     * @return $this|void
     */
    public function execute(EventObserver $observer)
    {

        $groupParams = $this->request->getParam('groups');

        $fields = $groupParams['general']['fields'];

        $data = $this->scopeConfig->getValue(
            'customer/captcha/forms',
            $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $scopeCode = null
        );

        if ($data !== null) {
            $formArray = explode(",", $data);

            if (in_array('user_forgotpassword', $formArray)) {
                if (isset($fields['enable']['value'])) {
                    if ($fields['enable']['value'] == '1') {
                        foreach (array_keys($formArray, 'user_forgotpassword') as $key) {
                            unset($formArray[$key]);
                        }
                        $this->setData(
                            'customer/captcha/forms',
                            implode(',', $formArray)
                        );
                    }
                }

                if (isset($fields['enable']['inherit'])) {
                    if ($fields['enable']['inherit'] == '1') {
                        foreach (array_keys($formArray, 'user_forgotpassword') as $key) {
                            unset($formArray[$key]);
                        }
                        $this->setData(
                            'customer/captcha/forms',
                            implode(',', $formArray)
                        );
                    }
                }
            }
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
