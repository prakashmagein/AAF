<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Model\Order;

use Magento\Store\Model\StoreManagerInterface;
use Magefan\OrderEdit\Model\Quote\TaxManager;

abstract class AbstractUpdateOrder implements \Magefan\OrderEdit\Api\UpdateOrderInterface
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    const SECTION_ORDER_INFO = 'order_info';
    const SECTION_ADDRESS = 'address';
    const SECTION_ITEMS = 'items';
    const SECTION_PAYMENT_METHOD = 'payment_method';
    const SECTION_SHIPPING_METHOD = 'shipping_method';

    /**
     * @var TaxManager
     */
    protected $taxManager;

    /**
     * @param StoreManagerInterface $storeManager
     * @param TaxManager $taxManager
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        TaxManager $taxManager
    ) {
        $this->storeManager = $storeManager;
        $this->taxManager = $taxManager;
    }

    /**
     * @param  array  $changes
     * @param  string $key
     * @param  string $nameOfField
     * @param  string $oldValue
     * @param  string $newValue
     * @return array
     */
    public function writeChanges(string $sectionName, array &$changes, string $key, string $nameOfField, string $oldValue, string $newValue): array
    {
        $changes[$sectionName][$key]['name_of_field'] = $nameOfField;
        $changes[$sectionName][$key]['old_value'] = $oldValue;
        $changes[$sectionName][$key]['new_value'] = $newValue;

        return $changes;
    }
}
