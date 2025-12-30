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

class Quantity implements ValidatorInterface
{
    /**
     * Validate coupon quantity for generation
     *
     * @param CouponGenerateInfoInterface $couponGenerateInfo
     * @throws LocalizedException
     */
    public function validate(CouponGenerateInfoInterface $couponGenerateInfo): void
    {
        if ($couponGenerateInfo->getQuantity() <= 0) {
            throw new LocalizedException(__(self::INVALID_FIELD_MIN_VALUE, [
                'fieldName' => 'Coupon Quantity',
                'value' => $couponGenerateInfo->getQuantity(),
                'minValue' => 0
            ]));
        }
    }
}
