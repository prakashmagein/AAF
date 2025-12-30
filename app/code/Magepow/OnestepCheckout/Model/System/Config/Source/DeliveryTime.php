<?php
/**
 * DeliveryTime
 *
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */
namespace Magepow\OnestepCheckout\Model\System\Config\Source;

use Magento\Framework\Model\AbstractModel;

class DeliveryTime extends AbstractModel
{
    const DAY_MONTH_YEAR = 'dd/mm/yy';
    const MONTH_DAY_YEAR = 'mm/dd/yy';
    const YEAR_MONTH_DAY = 'yy/mm/dd';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'label' => __('Day/Month/Year'),
                'value' => self::DAY_MONTH_YEAR
            ],
            [
                'label' => __('Month/Day/Year'),
                'value' => self::MONTH_DAY_YEAR
            ],
            [
                'label' => __('Year/Month/Day'),
                'value' => self::YEAR_MONTH_DAY
            ]
        ];

        return $options;
    }
}
