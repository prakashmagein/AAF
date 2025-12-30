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
declare(strict_types=1);

namespace Aheadworks\RewardPoints\Model\Source\Calculation;

use Magento\Framework\Data\OptionSourceInterface;

class PointsRoundingRule implements OptionSourceInterface
{
    /**#@+
     * Points rounding rule
     */
    const AUTO_ROUNDING = 'auto_rounding';
    const ROUND_DOWN = 'round_down';
    const ROUND_UP = 'round_up';
    /**#@-*/

    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::AUTO_ROUNDING,
                'label' => __('Auto rounding')
            ],
            [
                'value' => self::ROUND_DOWN,
                'label' => __('Round down > Round down (1.99 to 1)')
            ],
            [
                'value' => self::ROUND_UP,
                'label' => __('Round up > Round up (1.99 to 2)')
            ]
        ];
    }
}
