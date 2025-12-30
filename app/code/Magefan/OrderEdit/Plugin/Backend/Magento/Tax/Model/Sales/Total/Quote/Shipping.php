<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Plugin\Backend\Magento\Tax\Model\Sales\Total\Quote;

use Magento\Tax\Model\Sales\Total\Quote\Shipping as Subject;
use Magento\Framework\Registry;

class Shipping
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @param Registry $registry
     */
    public function __construct(
        Registry $registry
    ) {
        $this->registry = $registry;
    }

    /**
     * @param Shipping $subject
     * @param $quote
     * @param $shippingAssignment
     * @param $total
     * @return array
     */
    public function beforeCollect(Subject $subject, $quote, $shippingAssignment, $total)
    {
        $customerShippingPrice = $this->registry->registry('mf_order_edit_shipping_custom_price');
        $customerShippingBasePrice = $this->registry->registry('mf_order_edit_shipping_custom_base_price');

        if ((null !== $customerShippingPrice) && (null !== $customerShippingBasePrice)) {
            $total->setShippingAmount($customerShippingPrice);
            $total->setBaseShippingAmount($customerShippingBasePrice);
        }

        return [$quote, $shippingAssignment, $total];
    }
}
