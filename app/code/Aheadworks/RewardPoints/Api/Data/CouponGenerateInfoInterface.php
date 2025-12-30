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

interface CouponGenerateInfoInterface
{
    /**#@+
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case
     */
    public const QUANTITY = 'quantity';
    public const LENGTH = 'length';
    public const PREFIX = 'prefix';
    public const BALANCE = 'balance';
    /**#@-*/

    /**
     * Retrieve coupon quantity
     *
     * @return int
     */
    public function getQuantity(): int;

    /**
     * Set coupon quantity
     *
     * @param int $quantity
     * @return $this
     */
    public function setQuantity(int $quantity): self;

    /**
     * Retrieve code length
     *
     * @return int
     */
    public function getLength(): int;

    /**
     * Set code length
     *
     * @param int $length
     * @return $this
     */
    public function setLength(int $length): self;

    /**
     * Retrieve code prefix
     *
     * @return string
     */
    public function getPrefix(): string;

    /**
     * Set code prefix
     *
     * @param string $prefix
     * @return $this
     */
    public function setPrefix(string $prefix): self;

    /**
     * Retrieve coupon balance
     *
     * @return int
     */
    public function getBalance(): int;

    /**
     * Set coupon balance
     *
     * @param int $balance
     * @return $this
     */
    public function setBalance(int $balance): self;
}
