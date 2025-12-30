<?php
/**
 * Magedelight
 * Copyright (C) 2023 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_SMSProfile
 * @copyright Copyright (c) 2023 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */
 
namespace Magedelight\SMSProfile\Model\Config\Source;

class RecaptchaForms implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'create', 'label' => __('Registration Page')],
            ['value' => 'login', 'label' => __('Login Page')],
            ['value' => 'forgot', 'label' => __('Forgot Page')],
            ['value' => 'edit', 'label' => __('Edit Account Information Page')],
        ];
    }
}
