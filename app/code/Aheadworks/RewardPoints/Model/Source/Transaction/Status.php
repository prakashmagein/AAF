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
namespace Aheadworks\RewardPoints\Model\Source\Transaction;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Status
 *
 * @package Aheadworks\RewardPoints\Model\Source
 */
class Status implements ArrayInterface
{
    /**#@+
     * Entity type values
     */
    const ACTIVE = 1;
    const USED = 2;
    const EXPIRED = 3;
    const ON_HOLD = 4;
    const CANCELLED = 5;
    /**#@-*/

    /**
     * {@inheritDoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::ACTIVE,
                'label' => __('Active')
            ],
            [
                'value' => self::USED,
                'label' => __('Used')
            ],
            [
                'value' => self::EXPIRED,
                'label' => __('Expired')
            ],
            [
                'value' => self::ON_HOLD,
                'label' => __('On hold')
            ],
            [
                'value' => self::CANCELLED,
                'label' => __('Cancelled')
            ]
        ];
    }
}
