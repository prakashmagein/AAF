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
namespace Aheadworks\RewardPoints\Model\Source\Customer;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class BirthdayLimit
 * @package Aheadworks\RewardPoints\Model\Source\Customer
 */
class BirthdayLimit implements ArrayInterface
{
    /**#@+
     * Limit values
     */
    const NO_LIMIT = 'no_limit';
    const ONCE_A_YEAR = 'once_a_year';
    /**#@-*/

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::NO_LIMIT,
                'label' => __('No')
            ],
            [
                'value' => self::ONCE_A_YEAR,
                'label' => __('Once a year')
            ]
        ];
    }
}
