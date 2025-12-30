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

namespace Aheadworks\RewardPoints\Model\Calculator\Spending;

use Aheadworks\RewardPoints\Api\Data\SpendRateInterface;

/**
 * Interface SpendItemInterface
 */
interface SpendItemInterface
{
    /**
     * Get product id
     *
     * @return int|null
     */
    public function getProductId(): ?int;

    /**
     * Set product id
     *
     * @param int $productId
     * @return $this
     */
    public function setProductId(int $productId): SpendItemInterface;

    /**
     * Get base amount
     *
     * @return float|null
     */
    public function getBaseAmount(): ?float;

    /**
     * Set base amount
     *
     * @param float $baseAmount
     * @return $this
     */
    public function setBaseAmount(float $baseAmount): SpendItemInterface;

    /**
     * Get compensation tax amount
     *
     * @return float|null
     */
    public function getCompensationTaxAmount(): ?float;

    /**
     * Set compensation tax amount
     *
     * @param float $amount
     * @return $this
     */
    public function setCompensationTaxAmount(float $amount): SpendItemInterface;

    /**
     * Get base tax amount
     *
     * @return float|null
     */
    public function getBaseTaxAmount(): ?float;

    /**
     * Set base tax amount
     *
     * @param float $amount
     * @return $this
     */
    public function setBaseTaxAmount(float $amount): SpendItemInterface;

    /**
     * Get qty
     *
     * @return float|null
     */
    public function getQty(): ?float;

    /**
     * Set qty
     *
     * @param float $qty
     * @return $this
     */
    public function setQty(float $qty): SpendItemInterface;

    /**
     * Get points
     *
     * @return float|null
     */
    public function getPoints(): ?float;

    /**
     * Set points
     *
     * @param float $points
     * @return $this
     */
    public function setPoints(float $points): SpendItemInterface;

    /**
     * Get applied rule ids
     *
     * @return int[]|null
     */
    public function getAppliedRuleIds(): ?array;

    /**
     * Set applied rule ids
     *
     * @param int[] $ruleIds
     * @return $this
     */
    public function setAppliedRuleIds(array $ruleIds): SpendItemInterface;

    /**
     * Get share covered percent
     *
     * @return float|null
     */
    public function getShareCoveredPercent(): ?float;

    /**
     * Set share covered percent
     *
     * @param float $percent
     * @return SpendItemInterface
     */
    public function setShareCoveredPercent(float $percent): SpendItemInterface;
}
