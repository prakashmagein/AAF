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

use Aheadworks\RewardPoints\Api\Data\CouponInterface;
use Aheadworks\RewardPoints\Model\ResourceModel\Coupon as CouponResource;
use Aheadworks\RewardPoints\Model\Source\Coupon\Status\Enum as Status;
use Magento\Framework\Model\AbstractModel;

class Coupon extends AbstractModel implements CouponInterface
{
    /**
     * Set relation to resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(CouponResource::class);
    }

    /**
     * Retrieve coupon identifier
     *
     * @return int|null
     */
    public function getCouponId(): ?int
    {
        return (int) $this->getData(self::COUPON_ID) ?: null;
    }

    /**
     * Set coupon identifier
     *
     * @param int $couponId
     * @return $this
     */
    public function setCouponId(int $couponId): CouponInterface
    {
        return $this->setData(self::COUPON_ID, $couponId);
    }

    /**
     * Retrieve customer identifier
     *
     * @return int|null
     */
    public function getCustomerId(): ?int
    {
        return (int) $this->getData(self::CUSTOMER_ID) ?: null;
    }

    /**
     * Set customer identifier
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId(int $customerId): CouponInterface
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Retrieve code
     *
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->getData(self::CODE);
    }

    /**
     * Set code
     *
     * @param string $code
     * @return $this
     */
    public function setCode(string $code): CouponInterface
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * Retrieve status
     *
     * @return bool
     */
    public function getStatus(): bool
    {
        return (bool) $this->getData(self::STATUS);
    }

    /**
     * Set status
     *
     * @param Status $status
     * @return $this
     */
    public function setStatus(Status $status): CouponInterface
    {
        return $this->setData(self::STATUS, $status->value);
    }

    /**
     * Retrieve balance
     *
     * @return int
     */
    public function getBalance(): int
    {
        return (int) $this->getData(self::BALANCE);
    }

    /**
     * Set balance
     *
     * @param int $balance
     * @return $this
     */
    public function setBalance(int $balance): CouponInterface
    {
        return $this->setData(self::BALANCE, $balance);
    }

    /**
     * Retrieve creation time
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Set creation time
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt(string $createdAt): CouponInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Retrieve update time
     *
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * Set update time
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt(string $updatedAt): CouponInterface
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}
