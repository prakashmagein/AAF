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

namespace Aheadworks\RewardPoints\Model\Coupon\Generate;

use Aheadworks\RewardPoints\Api\Data\CouponGenerateInfoInterface;

use Aheadworks\RewardPoints\Api\CouponRepositoryInterface;
use Aheadworks\RewardPoints\Api\Data\CouponInterfaceFactory;
use Aheadworks\RewardPoints\Model\Source\Coupon\Status\Enum as CouponStatus;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random as RandomGenerator;
use Psr\Log\LoggerInterface;

class Consumer
{
    /**
     * @param LoggerInterface $logger
     * @param RandomGenerator $randomGenerator
     * @param CouponInterfaceFactory $couponFactory
     * @param CouponRepositoryInterface $couponRepository
     */
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly RandomGenerator $randomGenerator,
        private readonly CouponInterfaceFactory $couponFactory,
        private readonly CouponRepositoryInterface $couponRepository
    ) {}

    /**
     * Generate coupons through the queues
     *
     * @param CouponGenerateInfoInterface $couponGenerateInfo
     */
    public function process(
        CouponGenerateInfoInterface $couponGenerateInfo
    ): void {
        $quantity = $couponGenerateInfo->getQuantity();
        $length = $couponGenerateInfo->getLength();
        $prefix = $couponGenerateInfo->getPrefix();
        $balance = $couponGenerateInfo->getBalance();

        for ($i = 0; $i < $quantity; $i++) {
            try {
                $coupon = $this->couponFactory->create()
                    ->setCode($this->getRandomCode($prefix, $length))
                    ->setStatus(CouponStatus::ENABLED)
                    ->setBalance($balance);

                $this->couponRepository->save($coupon);
            } catch (LocalizedException $exception) {
                $this->logger->error($exception->getMessage());
            }
        }
    }

    /**
     * Generate random code for coupon
     *
     * @param string $prefix
     * @param int $length
     * @return string
     * @throws LocalizedException
     */
    private function getRandomCode(string $prefix, int $length): string
    {
        $chars = RandomGenerator::CHARS_UPPERS .RandomGenerator::CHARS_DIGITS;

        return $prefix .$this->randomGenerator->getRandomString($length, $chars);
    }
}
