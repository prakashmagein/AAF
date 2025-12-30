<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\OrderEdit\Api;

use Magento\Sales\Model\Order;
use Magento\Quote\Model\Quote;

/**
 * Interface UpdateOrderInterface
 */
interface UpdateOrderInterface
{
    /**
     * @param Order $order
     * @param array $logOfChanges
     * @param Quote|null $quote
     * @return bool
     */
    public function execute(Order $order, array &$logOfChanges, Quote $quote = null): bool;

    /**
     * @param string $sectionName
     * @param array $changes
     * @param string $key
     * @param string $nameOfField
     * @param string $oldValue
     * @param string $newValue
     * @return array
     */
    public function writeChanges(string $sectionName, array &$changes, string $key, string $nameOfField, string $oldValue, string $newValue): array;
}
