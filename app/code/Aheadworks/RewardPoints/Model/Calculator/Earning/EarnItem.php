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

use Magento\Framework\Api\AbstractSimpleObject;

/**
 * Class EarnItem
 */
class EarnItem extends AbstractSimpleObject implements EarnItemInterface
{
    /**#@+
     * Constants for keys.
     */
    const PRODUCT_ID    = 'product_id';
    const BASE_AMOUNT   = 'base_amount';
    const QTY           = 'qty';
    const POINTS        = 'points';
    /**#@-*/

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
    public function getBaseAmount()
    {
        return $this->_get(self::BASE_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseAmount($baseAmount)
    {
        return $this->setData(self::BASE_AMOUNT, $baseAmount);
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

    /**
     * {@inheritdoc}
     */
    public function getPoints()
    {
        return $this->_get(self::POINTS);
    }

    /**
     * {@inheritdoc}
     */
    public function setPoints($points)
    {
        return $this->setData(self::POINTS, $points);
    }
}
