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

use Aheadworks\RewardPoints\Api\CouponRepositoryInterface;
use Aheadworks\RewardPoints\Api\Data\CouponInterface;
use Aheadworks\RewardPoints\Model\ResourceModel\Coupon\Collection as CouponCollection;
use Magento\Framework\Exception\CouldNotDeleteException;

class Delete implements MassActionInterface
{
    /**
     * @param CouponRepositoryInterface $couponRepository
     */
    public function __construct(
        private readonly CouponRepositoryInterface $couponRepository
    ) {}

    /**
     * Delete massive action
     *
     * @param CouponCollection $couponCollection
     * @return string
     * @throws CouldNotDeleteException
     */
    public function execute(CouponCollection $couponCollection): string
    {
        $collectionSize = $couponCollection->getSize();

        /** @var CouponInterface $coupon */
        foreach ($couponCollection as $coupon) {
            $this->couponRepository->delete($coupon);
        }

        return __(
            'A total of %1 coupon(s) have been deleted.',
            $collectionSize
        )->render();
    }
}
