<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Model\SelectResolver\ColumnOrder;

interface OrderStorageInterface
{
    /**
     * Add order to storage
     *
     * @param string $columnName
     * @param string $direction
     */
    public function addOrder(string $columnName, string $direction): void;

    /**
     * Remove order from storage
     *
     * @param string $columnName
     */
    public function removeOrder(string $columnName): void;

    /**
     * Get all existed orders
     *
     * @return array
     */
    public function getAllOrders(): array;
}
