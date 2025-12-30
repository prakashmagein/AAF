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

namespace Aheadworks\RewardPoints\Model\Source\Coupon;

use Aheadworks\RewardPoints\Model\Source\Coupon\Status\Enum as StatusEnum;
use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    /**
     * Retrieve available coupon statuses
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => StatusEnum::DISABLED->value,
                'label' => __('Disabled')
            ],
            [
                'value' => StatusEnum::ENABLED->value,
                'label' => __('Enabled')
            ]
        ];
    }
}
