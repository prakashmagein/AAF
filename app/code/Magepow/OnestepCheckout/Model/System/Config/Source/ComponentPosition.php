<?php
/**
 * ComponentPosition
 *
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */
namespace Magepow\OnestepCheckout\Model\System\Config\Source;

use Magento\Framework\Model\AbstractModel;


class ComponentPosition extends AbstractModel
{
    const NOT_SHOW        = 0;
    const SHOW_IN_PAYMENT = 1;
    const SHOW_IN_REVIEW  = 2;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            self::NOT_SHOW        => __('No'),
            self::SHOW_IN_PAYMENT => __('In Payment Area'),
            self::SHOW_IN_REVIEW  => __('In Review Area')
        ];
    }
}
