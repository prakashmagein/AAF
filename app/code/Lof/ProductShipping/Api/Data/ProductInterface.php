<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\ProductShipping\Api\Data;

interface ProductInterface
{
    const LOFSHIPPING_ID = 'lofshipping_id';
    const PRODUCT_ID = 'product_id';
    const POSITION = 'position';

    /**
     * Get lofshipping_id
     * @return int|null
     */
    public function getLofshippingId();

    /**
     * Set lofshipping_id
     * @param int $lofshippingId
     * @return \Lof\ProductShipping\Api\Data\ProductInterface
     */
    public function setLofshippingId($lofshippingId);

    /**
     * Get product_id
     * @return int|null
     */
    public function getProductId();

    /**
     * Set product_id
     * @param int $product_id
     * @return \Lof\ProductShipping\Api\Data\ProductInterface
     */
    public function setProductId($product_id);

    /**
     * Get position
     * @return int|null
     */
    public function getPosition();

    /**
     * Set position
     * @param int $position
     * @return \Lof\ProductShipping\Api\Data\ProductInterface
     */
    public function setPosition($position);
}
