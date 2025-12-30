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

use Aheadworks\RewardPoints\Api\Data\CouponGenerateInfoInterface;
use Magento\Framework\DataObject;

class CouponGenerateInfo extends DataObject implements CouponGenerateInfoInterface
{
    /**
     * Retrieve coupon quantity
     *
     * @return int
     */
    public function getQuantity(): int
    {
        return (int) $this->getData(self::QUANTITY);
    }

    /**
     * Set coupon quantity
     *
     * @param int $quantity
     * @return $this
     */
    public function setQuantity(int $quantity): CouponGenerateInfoInterface
    {
        return $this->setData(self::QUANTITY, $quantity);
    }

    /**
     * Retrieve code length
     *
     * @return int
     */
    public function getLength(): int
    {
        return (int) $this->getData(self::LENGTH);
    }

    /**
     * Set code length
     *
     * @param int $length
     * @return $this
     */
    public function setLength(int $length): CouponGenerateInfoInterface
    {
        return $this->setData(self::LENGTH, $length);
    }

    /**
     * Retrieve code prefix
     *
     * @return string
     */
    public function getPrefix(): string
    {
        return (string) $this->getData(self::PREFIX);
    }

    /**
     * Set code prefix
     *
     * @param string $prefix
     * @return $this
     */
    public function setPrefix(string $prefix): CouponGenerateInfoInterface
    {
        return $this->setData(self::PREFIX, $prefix);
    }

    /**
     * Retrieve coupon balance
     *
     * @return int
     */
    public function getBalance(): int
    {
        return (int) $this->getData(self::BALANCE);
    }

    /**
     * Set coupon balance
     *
     * @param int $balance
     * @return $this
     */
    public function setBalance(int $balance): CouponGenerateInfoInterface
    {
        return $this->setData(self::BALANCE, $balance);
    }
}
