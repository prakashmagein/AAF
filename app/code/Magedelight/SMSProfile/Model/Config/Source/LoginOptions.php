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

class LoginOptions implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'login_pwd', 'label' => __('Login With Password Only')],
            ['value' => 'login_otp', 'label' => __('Login With OTP Only')],
            ['value' => 'login_both', 'label' => __('Login With OTP and Password')],
        ];
    }
}
