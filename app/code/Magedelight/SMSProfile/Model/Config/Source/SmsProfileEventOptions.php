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
 
namespace Magedelight\SMSProfile\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class SmsProfileEventOptions implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'customer_signup_otp', 'label' => __('Send Otp At Customer Signup Event')],
            ['value' => 'customer_login_otp', 'label' => __('Send Otp At Customer Login Event')],
            ['value' => 'customer_account_edit_otp', 'label' => __('Send Otp At Customer Account Update Event')],
            ['value' => 'cod_otp', 'label' => __('Send Otp For COD Payment Method During Checkout')],
            ['value' => 'forgot_password_otp', 'label' => __('Send Otp For Forgot Password Event')],
        ];
    }
}
