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
namespace Aheadworks\RewardPoints\Model\Calculator\Earning;

/**
 * Interface EarnItemInterface
 * @package Aheadworks\RewardPoints\Model\Calculator\Earning
 */
interface EarnItemInterface
{
    /**
     * Get product id
     *
     * @return int|null
     */
    public function getProductId();
    /**
     * Set product id
     *
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId);

    /**
     * Get base amount
     *
     * @return float|null
     */
    public function getBaseAmount();

    /**
     * Set base amount
     *
     * @param float $baseAmount
     * @return $this
     */
    public function setBaseAmount($baseAmount);

    /**
     * Get qty
     *
     * @return float|null
     */
    public function getQty();

    /**
     * Set qty
     *
     * @param float $qty
     * @return $this
     */
    public function setQty($qty);

    /**
     * Get points
     *
     * @return float|null
     */
    public function getPoints();

    /**
     * Set points
     *
     * @param float $points
     * @return $this
     */
    public function setPoints($points);
}
