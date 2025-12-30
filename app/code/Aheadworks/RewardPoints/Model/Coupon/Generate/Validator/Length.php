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

namespace Aheadworks\RewardPoints\Model\Coupon\Generate\Validator;

use Aheadworks\RewardPoints\Api\Data\CouponGenerateInfoInterface;
use Magento\Framework\Exception\LocalizedException;

class Length implements ValidatorInterface
{
    /**
     * Validate code length for coupon generation
     *
     * @param CouponGenerateInfoInterface $couponGenerateInfo
     * @throws LocalizedException
     */
    public function validate(CouponGenerateInfoInterface $couponGenerateInfo): void
    {
        if ($couponGenerateInfo->getLength() <= 3) {
            throw new LocalizedException(__(self::INVALID_FIELD_MIN_VALUE, [
                'fieldName' => 'Code Length',
                'value' => $couponGenerateInfo->getLength(),
                'minValue' => 3
            ]));
        }
    }
}
