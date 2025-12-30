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

namespace Aheadworks\RewardPoints\Model\Coupon\MassAction;

use Aheadworks\RewardPoints\Model\ResourceModel\Coupon\Collection as CouponCollection;
use Magento\Framework\Exception\LocalizedException;

interface MassActionInterface
{
    /**
     * Perform massive action
     *
     * @param CouponCollection $couponCollection
     * @return string
     * @throws LocalizedException
     */
    public function execute(CouponCollection $couponCollection): string;
}
