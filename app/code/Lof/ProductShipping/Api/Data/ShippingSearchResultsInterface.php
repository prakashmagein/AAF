<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\ProductShipping\Api\Data;

interface ShippingSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Shipping list.
     * @return \Lof\ProductShipping\Api\Data\ShippingInterface[]
     */
    public function getItems();

    /**
     * Set lofshipping_id list.
     * @param \Lof\ProductShipping\Api\Data\ShippingInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

