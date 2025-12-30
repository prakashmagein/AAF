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

class SmsPromotionalOptions implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $res = [];
        $res[] = ['value' => 'custom', 'label' => 'Custom No.'];
        $res[] = ['value' => 'customer', 'label' => 'Customer'];
        $res[] = ['value' => 'customer_group', 'label' => 'Customer Group'];
        $res[] = ['value' => 'abandoned_cart', 'label' => 'Abandoned Cart'];
        $res[] = ['value' => 'subscriber', 'label' => 'Newsletter Subscribed Customer'];
        $res[] = ['value' => 'csv', 'label' => 'Import CSV'];
        return $res;
    }
}
