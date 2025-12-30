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
namespace Aheadworks\RewardPoints\Model\Config\Source;

/**
 * Class Aheadworks\RewardPoints\Model\Config\Source\SocialButtonStyle
 */
class SocialButtonStyle implements \Magento\Framework\Option\ArrayInterface
{
    /**#@+
     * Social Button Style
     */
    const ICONS_ONLY_STYLE = 'icons_only';
    const ICONS_WITH_COUNTER_V_STYLE = 'icons_with_counter_v';
    const ICONS_WITH_COUNTER_H_STYLE = 'icons_with_counter_h';
    /**#@-*/

    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            self::ICONS_ONLY_STYLE => __('Icons Only'),
            self::ICONS_WITH_COUNTER_V_STYLE => __('Icons with Counter (vertical)'),
            self::ICONS_WITH_COUNTER_H_STYLE => __('Icons with Counter (horizontal)'),
        ];
    }
}
