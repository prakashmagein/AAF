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

use Magento\Checkout\Model\ConfigProviderInterface;
use Magedelight\SMSProfile\Helper\Data as HelperData;

class SmsprofileConfigProvider implements ConfigProviderInterface
{

    /** @var HelperData */
    private $datahelper;

    /** @var \Magento\Customer\Model\Session */
    private $session;

    /**
     * Constructor
     * @param HelperData $dataHelper
     * @param \Magento\Customer\Model\Session $session
     */

    public function __construct(
        HelperData $dataHelper,
        \Magento\Customer\Model\Session $session
    ) {
        $this->datahelper = $dataHelper;
        $this->session = $session;
    }

    public function getConfig()
    {
        $config = [];
        $config['customer_country_enabled']=0;
        $phoneValidation = $this->datahelper->getPhoneValidation();
        if ($this->datahelper->getModuleStatus()) {
            $config['otplogin'] = $this->datahelper->getSmsProfileOtpOnLogin();
            $config['enable_on_checkoutpage'] = ($this->datahelper->getSmsProfileOnCheckoutPage() && $this->datahelper->getGuestCheckoutLogin())?$this->datahelper->getSmsProfileOnCheckoutPage():0;
            if ($this->datahelper->isCustomerCountryEnabled()) {
                $config['customer_country_enabled'] = $this->datahelper->isCustomerCountryEnabled();
                $onlyCountries= ($this->datahelper->getAvailableCountries()) ? $this->datahelper->getAvailableCountriesCheckout() : [];
                $config['only_countries'] = $onlyCountries;
                $preferredCountries=($this->datahelper->getDefaultCustomerCountry()) ? explode(',', $this->datahelper->getDefaultCustomerCountry() ?? ''):[];
                $config['preferred_countries'] = $preferredCountries;
            } else {
                $config['mobile_default_validation'] = 10;
                for ($i=0; $i < count($phoneValidation); $i++) {
                    if ($phoneValidation[$i]['country']=='default') {
                        $config['mobile_default_validation'] = $phoneValidation[$i]['digit'];
                    }
                }
            }
            if ($this->datahelper->getPhoneNote()) {
                $config['otpnote'] = $this->datahelper->getPhoneNote();
            }
            $config['auto_verify_otp'] = $this->datahelper->getAutoVerifyOTP();
            $config['resend_limit'] = $this->datahelper->getOTPResendLimit();
            $config['resend_limit_time'] = $this->datahelper->getOTPResendTime();
            $config['resend_link_disable'] = ($this->datahelper->getOTPResendLimitEnable())?0:1;
            $config['phone_validation'] = json_encode($phoneValidation);
            $config['otpcod']= $this->datahelper->getOtpForCOD();
            $config['otpresend_limit'] = $this->datahelper->getOTPResendLimit();
        } else {
            $config['otplogin'] = 'login_pwd';
            $config['enable_on_checkoutpage'] = 0;
            $config['otpcod']=0;
        }
        
        return $config;
    }
}
