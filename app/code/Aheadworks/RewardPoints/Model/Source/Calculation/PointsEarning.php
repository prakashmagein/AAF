<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://aheadworks.com/end-user-license-agreement/
 *
 * @package    RewardPoints
 * @version    2.4.0
 * @copyright  Copyright (c) 2024 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\RewardPoints\Model\Source\Calculation;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class PointsEarning
 *
 * @package Aheadworks\RewardPoints\Model\Source\Calculation
 */
class PointsEarning implements ArrayInterface
{
    /**#@+
     * Points earning values
     */
    const BEFORE_TAX = 1;
    const AFTER_TAX = 2;
    /**#@-*/

    /**
     *  {@inheritDoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::BEFORE_TAX,
                'label' => __('Before Tax')
            ],
            [
                'value' => self::AFTER_TAX,
                'label' => __('After Tax')
            ]
        ];
    }
}
