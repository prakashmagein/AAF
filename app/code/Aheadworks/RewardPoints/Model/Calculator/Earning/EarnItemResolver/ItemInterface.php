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

namespace Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver;

/**
 * Class Item
 */
interface ItemInterface
{
    /**
     * Get parent item
     *
     * @return ItemInterface|null
     */
    public function getParentItem();

    /**
     * Set parent item
     *
     * @param ItemInterface $item
     * @return $this
     */
    public function setParentItem($item);

    /**
     * Get product id
     *
     * @return int
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
     * Get product type
     *
     * @return string
     */
    public function getProductType();

    /**
     * Set product type
     *
     * @param string $productType
     * @return $this
     */
    public function setProductType($productType);

    /**
     * Get is children calculated
     *
     * @return string
     */
    public function getIsChildrenCalculated();

    /**
     * Set is children calculated
     *
     * @param bool $isChildrenCalculated
     * @return $this
     */
    public function setIsChildrenCalculated($isChildrenCalculated);

    /**
     * Get the base row total
     *
     * @return float
     */
    public function getBaseRowTotal();

    /**
     * Set the base row total
     *
     * @param float $amount
     * @return $this
     */
    public function setBaseRowTotal($amount);

    /**
     * Get the base row total with tax
     *
     * @return float
     */
    public function getBaseRowTotalInclTax();

    /**
     * Set the base row total with tax
     *
     * @param float $amount
     * @return $this
     */
    public function setBaseRowTotalInclTax($amount);

    /**
     * Get the base discount amount
     *
     * @return float
     */
    public function getBaseDiscountAmount();

    /**
     * Set the base discount amount
     *
     * @param float $amount
     * @return $this
     */
    public function setBaseDiscountAmount($amount);

    /**
     * Get the amount for other deduction
     *
     * @return float|null
     */
    public function getAwRpAmountForOtherDeduction(): ?float;

    /**
     * Set the amount for other deduction
     *
     * @param float|null $amount
     * @return $this
     */
    public function setAwRpAmountForOtherDeduction(?float $amount): self;

    /**
     * Get the base reward points amount (Aw)
     *
     * @return float
     */
    public function getBaseAwRewardPointsAmount();

    /**
     * Set the base reward points amount (Aw)
     *
     * @param float $amount
     * @return $this
     */
    public function setBaseAwRewardPointsAmount($amount);

    /**
     * Get the quantity
     *
     * @return float
     */
    public function getQty();

    /**
     * Set the quantity
     *
     * @param float $qty
     * @return $this
     */
    public function setQty($qty);
}
