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

namespace Aheadworks\RewardPoints\Api\Data;

use Aheadworks\RewardPoints\Model\Source\Coupon\Status\Enum as Status;

interface CouponInterface
{
    /**#@+
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case
     */
    public const COUPON_ID = 'coupon_id';
    public const CUSTOMER_ID = 'customer_id';
    public const CODE = 'code';
    public const STATUS = 'status';
    public const BALANCE = 'balance';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    /**#@-*/

    /**
     * Retrieve coupon identifier
     *
     * @return int|null
     */
    public function getCouponId(): ?int;

    /**
     * Set coupon identifier
     *
     * @param int $couponId
     * @return $this
     */
    public function setCouponId(int $couponId): self;

    /**
     * Retrieve customer identifier
     *
     * @return int|null
     */
    public function getCustomerId(): ?int;

    /**
     * Set customer identifier
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId(int $customerId): self;

    /**
     * Retrieve code
     *
     * @return string|null
     */
    public function getCode(): ?string;

    /**
     * Set code
     *
     * @param string $code
     * @return $this
     */
    public function setCode(string $code): self;

    /**
     * Retrieve status
     *
     * @return bool
     */
    public function getStatus(): bool;

    /**
     * Set status
     *
     * @param Status $status
     * @return $this
     */
    public function setStatus(Status $status): self;

    /**
     * Retrieve balance
     *
     * @return int
     */
    public function getBalance(): int;

    /**
     * Set balance
     *
     * @param int $balance
     * @return $this
     */
    public function setBalance(int $balance): self;

    /**
     * Retrieve creation time
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * Set creation time
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt(string $createdAt): self;

    /**
     * Retrieve update time
     *
     * @return string|null
     */
    public function getUpdatedAt(): ?string;

    /**
     * Set update time
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt(string $updatedAt): self;
}
