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

use Magento\Framework\Api\AbstractSimpleObject;

/**
 * Class SpendItem
 */
class SpendItem extends AbstractSimpleObject implements SpendItemInterface
{
    /**#@+
     * Constants for keys.
     */
    const PRODUCT_ID = 'product_id';
    const BASE_AMOUNT = 'base_amount';
    const QTY = 'qty';
    const POINTS = 'points';
    const APPLIED_RULE_IDS = 'applied_rule_ids';
    const SHARE_COVERED_PERCENT = 'share_covered_percent';
    const COMPENSATION_TAX_AMOUNT = 'compensation_tax_amount';
    const BASE_TAX_AMOUNT = 'base_tax_amount';
    /**#@-*/

    /**
     * Get product id
     *
     * @return int|null
     */
    public function getProductId(): ?int
    {
        return $this->_get(self::PRODUCT_ID);
    }

    /**
     * Set product id
     *
     * @param int $productId
     * @return $this
     */
    public function setProductId(int $productId): SpendItemInterface
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * Get base amount
     *
     * @return float|null
     */
    public function getBaseAmount(): ?float
    {
        return $this->_get(self::BASE_AMOUNT);
    }

    /**
     * Set base amount
     *
     * @param float $baseAmount
     * @return $this
     */
    public function setBaseAmount(float $baseAmount): SpendItemInterface
    {
        return $this->setData(self::BASE_AMOUNT, $baseAmount);
    }

    /**
     * Get compensation tax amount
     *
     * @return float|null
     */
    public function getCompensationTaxAmount(): ?float
    {
        return $this->_get(self::COMPENSATION_TAX_AMOUNT);
    }

    /**
     * Set compensation tax amount
     *
     * @param float $amount
     * @return $this
     */
    public function setCompensationTaxAmount(float $amount): SpendItemInterface
    {
        return $this->setData(self::COMPENSATION_TAX_AMOUNT, $amount);
    }

    /**
     * Get base tax amount
     *
     * @return float|null
     */
    public function getBaseTaxAmount(): ?float
    {
        return $this->_get(self::BASE_TAX_AMOUNT);
    }

    /**
     * Set base tax amount
     *
     * @param float $amount
     * @return $this
     */
    public function setBaseTaxAmount(float $amount): SpendItemInterface
    {
        return $this->setData(self::BASE_TAX_AMOUNT, $amount);
    }

    /**
     * Get qty
     *
     * @return float|null
     */
    public function getQty(): ?float
    {
        return $this->_get(self::QTY);
    }

    /**
     * Set qty
     *
     * @param float $qty
     * @return $this
     */
    public function setQty(float $qty): SpendItemInterface
    {
        return $this->setData(self::QTY, $qty);
    }

    /**
     * Get points
     *
     * @return float|null
     */
    public function getPoints(): ?float
    {
        return $this->_get(self::POINTS);
    }

    /**
     * Set points
     *
     * @param float $points
     * @return $this
     */
    public function setPoints(float $points): SpendItemInterface
    {
        return $this->setData(self::POINTS, $points);
    }


    /**
     * Get applied rule ids
     *
     * @return int[]|null
     */
    public function getAppliedRuleIds(): ?array
    {
        return $this->_get(self::APPLIED_RULE_IDS);
    }

    /**
     * Set applied rule ids
     *
     * @param int[] $ruleIds
     * @return $this
     */
    public function setAppliedRuleIds(array $ruleIds): SpendItemInterface
    {
        return $this->setData(self::APPLIED_RULE_IDS, $ruleIds);
    }

    /**
     * Get share covered percent
     *
     * @return float|null
     */
    public function getShareCoveredPercent(): ?float
    {
        return $this->_get(self::SHARE_COVERED_PERCENT);
    }

    /**
     * Set share covered percent
     *
     * @param float $percent
     * @return SpendItemInterface
     */
    public function setShareCoveredPercent(float $percent): SpendItemInterface
    {
        return $this->setData(self::SHARE_COVERED_PERCENT, $percent);
    }
}
