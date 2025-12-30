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
namespace Aheadworks\RewardPoints\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class NotifiedStatus
 *
 * @package Aheadworks\RewardPoints\Model\Source
 */
class NotifiedStatus implements ArrayInterface
{
    /**#@+
     * Notified status values
     */
    const YES = 1;
    const NO = 2;
    const NOT_SUBSCRIBED = 3;
    const WAITING = 4;
    const CANCELLED = 5;
    /**#@-*/

    /**
     *  {@inheritDoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::YES,
                'label' => __('Yes')
            ],
            [
                'value' => self::NO,
                'label' => __('No')
            ],
            [
                'value' => self::NOT_SUBSCRIBED,
                'label' => __('Not Subscribed')
            ],
            [
                'value' => self::WAITING,
                'label' => __('Waiting')
            ],
            [
                'value' => self::CANCELLED,
                'label' => __('Cancelled')
            ]
        ];
    }
}
