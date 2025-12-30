<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_ProductShipping
 * @copyright  Copyright (c) 2022 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
declare(strict_types=1);

namespace Lof\ProductShipping\Model;

use Lof\ProductShipping\Api\Data\ProductInterface;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * @inheritdoc
 */
class Product extends AbstractSimpleObject implements ProductInterface
{
    /**
     * @inheritDoc
     */
    public function getLofshippingId()
    {
        return $this->_get(self::LOFSHIPPING_ID);
    }

    /**
     * @inheritDoc
     */
    public function setLofshippingId($lofshippingId)
    {
        return $this->setData(self::LOFSHIPPING_ID, $lofshippingId);
    }

    /**
     * @inheritDoc
     */
    public function getProductId()
    {
        return $this->_get(self::PRODUCT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setProductId($product_id)
    {
        return $this->setData(self::PRODUCT_ID, $product_id);
    }

    /**
     * @inheritDoc
     */
    public function getPosition()
    {
        return $this->_get(self::POSITION);
    }

    /**
     * @inheritDoc
     */
    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
    }

}
