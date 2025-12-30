<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_ProductShipping
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\ProductShipping\Plugin\Quote;

use Lof\ProductShipping\Model\Carrier;

class ShipmentEstimation
{
    const FREESHIPPING = "freeshipping";
    const TABLERATE = "tablerate";
    /**
     * @var \Lof\ProductShipping\Helper\Data
     */
    protected $helperData;

    /**
     * @param \Lof\ProductShipping\Helper\Data $helperData
     */
    public function __construct(
        \Lof\ProductShipping\Helper\Data $helperData
    )
    {
        $this->helperData = $helperData;
    }
    /**
     * @inheritdoc
     */
    public function afterEstimateByExtendedAddress(\Magento\Quote\Model\ShippingMethodManagement $subject, $result)
    {
        if ($this->helperData->getIsDisableFreeShipping()) {
            $productShipping = false;
            $freeshipping = false;
            $tablerateshipping = false;
            if ($result) {
                foreach ($result as $shipping) {
                    switch($shipping->getCarrierCode()) {
                        case Carrier::CODE:
                            $productShipping = $shipping;
                            break;
                        case self::FREESHIPPING:
                            $freeshipping = $shipping;
                            break;
                        case self::TABLERATE:
                            if ((float)$shipping->getAmount() > 0) {
                                $tablerateshipping = $shipping;
                            }
                            break;
                        default:
                            break;
                    }
                }
                if ($productShipping && ($freeshipping || $tablerateshipping)) {
                    $excludeShippingTypes = $this->getShippingTypes();
                    $newResult = [];
                    foreach ($result as $shipping) {
                        if (!in_array($shipping->getCarrierCode(), $excludeShippingTypes)) {
                            $newResult[] = $shipping;
                        }
                    }
                    if (count($newResult)) {
                        $result = $newResult;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * get ignore shipping types
     *
     * @return array
     */
    protected function getShippingTypes()
    {
        return [
            self::FREESHIPPING,
            self::TABLERATE
        ];
    }
}
