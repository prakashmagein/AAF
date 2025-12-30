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

namespace Aheadworks\RewardPoints\Api;

use Aheadworks\RewardPoints\Api\Data\CouponInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

interface CouponRepositoryInterface
{
    /**
     * Save coupon
     *
     * @param CouponInterface $coupon
     * @return CouponInterface
     * @throws CouldNotSaveException
     */
    public function save(CouponInterface $coupon): CouponInterface;

    /**
     * Retrieve coupon by identifier
     *
     * @param int $couponId
     * @return CouponInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $couponId): CouponInterface;

    /**
     * Retrieve coupon by code
     *
     * @param string $code
     * @return CouponInterface
     * @throws NoSuchEntityException
     */
    public function getByCode(string $code): CouponInterface;

    /**
     * Retrieve coupons matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return CouponInterface[]
     */
    public function getList(SearchCriteriaInterface $searchCriteria): array;

    /**
     * Delete coupon
     *
     * @param CouponInterface $coupon
     * @throws CouldNotDeleteException
     */
    public function delete(CouponInterface $coupon): void;
}
