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

use Magento\Framework\Api\AbstractSimpleObject;

/**
 * Class Item
 */
class Item extends AbstractSimpleObject implements ItemInterface
{
    /**#@+
     * Constants for keys.
     */
    const PARENT_ITEM                       = 'parent_item';
    const PRODUCT_ID                        = 'product_id';
    const PRODUCT_TYPE                      = 'product_type';
    const IS_CHILDREN_CALCULATED            = 'is_children_calculated';
    const BASE_ROW_TOTAL                    = 'base_row_total';
    const BASE_ROW_TOTAL_INCL_TAX           = 'base_row_total_incl_tax';
    const BASE_DISCOUNT_AMOUNT              = 'base_discount_amount';
    const BASE_AW_REWARD_POINTS_AMOUNT      = 'base_aw_reward_points_amount';
    const QTY                               = 'qty';
    const AW_RP_AMOUNT_FOR_OTHER_DEDUCTION  = 'aw_rp_amount_for_other_deduction';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function getParentItem()
    {
        return $this->_get(self::PARENT_ITEM);
    }

    /**
     * {@inheritdoc}
     */
    public function setParentItem($item)
    {
        return $this->setData(self::PARENT_ITEM, $item);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductId()
    {
        return $this->_get(self::PRODUCT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductType()
    {
        return $this->_get(self::PRODUCT_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductType($productType)
    {
        return $this->setData(self::PRODUCT_TYPE, $productType);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsChildrenCalculated()
    {
        return $this->_get(self::IS_CHILDREN_CALCULATED);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsChildrenCalculated($isChildrenCalculated)
    {
        return $this->setData(self::IS_CHILDREN_CALCULATED, $isChildrenCalculated);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseRowTotal()
    {
        return $this->_get(self::BASE_ROW_TOTAL);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseRowTotal($amount)
    {
        return $this->setData(self::BASE_ROW_TOTAL, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseRowTotalInclTax()
    {
        return $this->_get(self::BASE_ROW_TOTAL_INCL_TAX);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseRowTotalInclTax($amount)
    {
        return $this->setData(self::BASE_ROW_TOTAL_INCL_TAX, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseDiscountAmount()
    {
        return $this->_get(self::BASE_DISCOUNT_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseDiscountAmount($amount)
    {
        return $this->setData(self::BASE_DISCOUNT_AMOUNT, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getAwRpAmountForOtherDeduction(): ?float
    {
        return $this->_get(self::AW_RP_AMOUNT_FOR_OTHER_DEDUCTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setAwRpAmountForOtherDeduction(?float $amount): self
    {
        return $this->setData(self::AW_RP_AMOUNT_FOR_OTHER_DEDUCTION, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseAwRewardPointsAmount()
    {
        return $this->_get(self::BASE_AW_REWARD_POINTS_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseAwRewardPointsAmount($amount)
    {
        return $this->setData(self::BASE_AW_REWARD_POINTS_AMOUNT, $amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getQty()
    {
        return $this->_get(self::QTY);
    }

    /**
     * {@inheritdoc}
     */
    public function setQty($qty)
    {
        return $this->setData(self::QTY, $qty);
    }
}
