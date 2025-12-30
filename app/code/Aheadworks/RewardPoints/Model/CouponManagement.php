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

use Aheadworks\RewardPoints\Api\CouponManagementInterface;
use Aheadworks\RewardPoints\Api\CouponRepositoryInterface;
use Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface;
use Aheadworks\RewardPoints\Api\Data\CouponInterface;
use Aheadworks\RewardPoints\Api\TransactionManagementInterface;
use Aheadworks\RewardPoints\Model\Source\Coupon\Status\Enum as CouponStatus;
use Aheadworks\RewardPoints\Model\Source\Transaction\Type as TransactionType;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class CouponManagement implements CouponManagementInterface
{
    /**
     * @param ResourceConnection $resourceConnection
     * @param CouponRepositoryInterface $couponRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param TransactionManagementInterface $transactionManagement
     * @param CustomerRewardPointsManagementInterface $customerRewardPointsManagement
     * @param ExpirationDate $expirationDate
     */
    public function __construct(
        private readonly ResourceConnection $resourceConnection,
        private readonly CouponRepositoryInterface $couponRepository,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly TransactionManagementInterface $transactionManagement,
        private readonly CustomerRewardPointsManagementInterface $customerRewardPointsManagement,
        private readonly ExpirationDate $expirationDate
    ) {}

    /**
     * Apply coupon by code
     *
     * @param string $code
     * @param int $customerId
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function apply(string $code, int $customerId): void
    {
        $coupon = $this->couponRepository->getByCode($code);
        $customer = $this->customerRepository->getById($customerId);

        if (!$coupon->getStatus()) {
            throw new LocalizedException(
                __('Coupon "%1" has already been used.', $coupon->getCode())
            );
        }

        $coupon
            ->setCustomerId((int) $customer->getId())
            ->setStatus(CouponStatus::DISABLED);

        $connection = $this->resourceConnection->getConnection();
        $connection->beginTransaction();

        try {
            $this->couponRepository->save($coupon);
            $this->createTransaction($coupon, $customer);
            $connection->commit();
        } catch (CouldNotSaveException $exception) {
            $connection->rollBack();
            throw $exception;
        }
    }

    /**
     * Create reward transaction for the coupon
     *
     * @param CouponInterface $coupon
     * @param CustomerInterface $customer
     * @throws CouldNotSaveException
     */
    private function createTransaction(
        CouponInterface $coupon,
        CustomerInterface $customer
    ): void {
        $transaction = $this->transactionManagement->createTransaction(
            $customer,
            $coupon->getBalance(),
            $this->expirationDate->getExpirationDate((int)$customer->getWebsiteId()),
            __('Coupon "%1"', $coupon->getCode())->render()
        );

        $currentBalance = $this->customerRewardPointsManagement->getCustomerRewardPointsBalance(
            $customer->getId()
        );

        $transaction
            ->setCurrentBalance($currentBalance)
            ->setType(TransactionType::POINTS_REWARDED_FOR_COUPON);

        $this->transactionManagement->saveTransaction($transaction);
    }
}
