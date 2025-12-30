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
 
namespace Magedelight\SMSProfile\Model\Config\Source;

class PhoneAttribute implements \Magento\Framework\Option\ArrayInterface
{
    const CUSTOM_CUSTOMER_ATTR = 1;
    const TELEPHONE_ADDRESS_ATTR = 2;

    public function toOptionArray()
    {
        return [
            ['value' => self::CUSTOM_CUSTOMER_ATTR, 'label' => __('Custom Customer Attribute')],
            ['value' => self::TELEPHONE_ADDRESS_ATTR, 'label' => __('Telephone Address Attribute')],
        ];
    }
}
