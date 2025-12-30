<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_ProductShipping
 * @copyright  Copyright (c) 2022 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\ProductShipping\Model\Config\Source;

class Country implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Lof\ProductShipping\Block\Shipping\Shipping
     */
    protected $shippingBlock;

    /**
     * @param \Lof\ProductShipping\Block\Shipping\Shipping $shippingBlock
     */
    public function __construct(\Lof\ProductShipping\Block\Shipping\Shipping $shippingBlock)
    {
        $this->shippingBlock = $shippingBlock;
    }

    /**
     * Options getter
     *
     * @return mixed|array
     */
    public function toOptionArray()
    {
        $country = $this->shippingBlock->getCountryOptionArray();
        $data = array();

        foreach ($country as $key => $_country) {
            $data[] = [
                'value' => $_country['value'],
                'label' => $_country['label']
            ];
        }

        return $data;
    }

}
