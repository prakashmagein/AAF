<?php
/**
 * Totals
 *
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */
namespace Magepow\OnestepCheckout\Block\LayoutOnestep\Order;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;

class Totals extends Template
{

    public function initTotals()
    {
        $totalsBlock = $this->getParentBlock();
        $source      = $totalsBlock->getSource();
        if ($source && !empty($source->getGiftWrapAmount())) {
            $totalsBlock->addTotal(new DataObject([
                'code'  => 'gift_wrap',
                'field' => 'onestepcheckout_gift_wrap_amount',
                'label' => __('Gift Wrap'),
                'value' => $source->getGiftWrapAmount(),
            ]));
        }
    }
}
