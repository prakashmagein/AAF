<?php

/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @author     Hassan <support@fmeextensions.com>
 * @package   FME_Refund
 * @copyright Copyright (c) 2021 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */

    namespace FME\Refund\Helper;

    use Magento\Framework\App\Helper\AbstractHelper;
    use Magento\Store\Model\ScopeInterface;

    class Data extends AbstractHelper
    {
        const XML_PATH_MANAGER = 'request_manage/';

        public function getConfigValue($field, $storeCode = null)
        {
            return $this->scopeConfig->getValue($field, ScopeInterface::SCOPE_STORE, $storeCode);
        }

        public function getGeneralConfig($fieldid, $storeCode = null)
        {
            return $this->getConfigValue(self::XML_PATH_MANAGER.'general/'.$fieldid, $storeCode);
        }
        public function getRecaptchaConfig($fieldid, $storeCode = null)
        {
            return $this->getConfigValue(self::XML_PATH_MANAGER.'grecaptcha/'.$fieldid, $storeCode);
        }
        public function getPopupConfig($fieldid, $storeCode = null)
        {
            $isAllowed =  $this->getConfigValue(self::XML_PATH_MANAGER.'popup/'.$fieldid, $storeCode);
            return $isAllowed;
        }
        
    }

