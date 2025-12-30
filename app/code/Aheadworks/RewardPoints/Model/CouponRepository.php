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

namespace Aheadworks\RewardPoints\Model;

use Aheadworks\RewardPoints\Api\CouponRepositoryInterface;
use Aheadworks\RewardPoints\Api\Data\CouponInterface;
use Aheadworks\RewardPoints\Api\Data\CouponInterfaceFactory;
use Aheadworks\RewardPoints\Model\ResourceModel\Coupon as CouponResource;
use Aheadworks\RewardPoints\Model\ResourceModel\Coupon\CollectionFactory as CouponCollectionFactory;
use Exception;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class CouponRepository implements CouponRepositoryInterface
{
    /**
     * @param CouponResource $couponResource
     * @param CouponInterfaceFactory $couponFactory
     * @param CouponCollectionFactory $couponCollectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        private readonly CouponResource $couponResource,
        private readonly CouponInterfaceFactory $couponFactory,
        private readonly CouponCollectionFactory $couponCollectionFactory,
        private readonly CollectionProcessorInterface $collectionProcessor
    ) {}

    /**
     * Save coupon
     *
     * @param CouponInterface $coupon
     * @return CouponInterface
     * @throws CouldNotSaveException
     */
    public function save(CouponInterface $coupon): CouponInterface
    {
        try {
            /** @var Coupon $coupon */
            $this->couponResource->save($coupon);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(
                __($exception->getMessage())
            );
        }

        return $coupon;
    }

    /**
     * Retrieve coupon by identifier
     *
     * @param int $couponId
     * @return CouponInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $couponId): CouponInterface
    {
        return $this->getByField(CouponInterface::COUPON_ID, $couponId);
    }

    /**
     * Retrieve coupon by code
     *
     * @param string $code
     * @return CouponInterface
     * @throws NoSuchEntityException
     */
    public function getByCode(string $code): CouponInterface
    {
        return $this->getByField(CouponInterface::CODE, $code);
    }

    /**
     * Retrieve coupons matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return CouponInterface[]
     */
    public function getList(SearchCriteriaInterface $searchCriteria): array
    {
        $couponCollection = $this->couponCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $couponCollection);

        /** @var CouponInterface[] $coupons */
        $coupons = $couponCollection->getItems();

        return $coupons;
    }

    /**
     * Delete coupon
     *
     * @param CouponInterface $coupon
     * @throws CouldNotDeleteException
     */
    public function delete(CouponInterface $coupon): void
    {
        try {
            /** @var Coupon $coupon */
            $this->couponResource->delete($coupon);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(
                __($exception->getMessage())
            );
        }
    }

    /**
     * Retrieve coupon by field value
     *
     * @param string $field
     * @param string|int $value
     * @return CouponInterface
     * @throws NoSuchEntityException
     */
    private function getByField(string $field, $value): CouponInterface
    {
        /** @var Coupon $coupon */
        $coupon = $this->couponFactory->create();

        $this->couponResource->load($coupon, $value, $field);
        if (!$coupon->getCouponId()) {
            throw NoSuchEntityException::singleField($field, $value);
        }

        return $coupon;
    }
}
