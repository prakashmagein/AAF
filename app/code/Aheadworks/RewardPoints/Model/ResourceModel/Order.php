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
namespace Aheadworks\RewardPoints\Model\ResourceModel;

use Magento\Sales\Model\Order as SalesOrder;

/**
 * Flat sales order resource
 */
class Order extends \Magento\Sales\Model\ResourceModel\Order
{
    /**
     * Check if customer ordered $productId
     *
     * @param int $customerId
     * @param int $productId
     * @return boolean
     */
    public function isCustomersOwnerOfProductId($customerId, $productId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), 'entity_id')
            ->joinInner(
                ['items' => $this->getTable('sales_order_item')],
                'entity_id = items.order_id AND items.product_id = ' . $productId,
                ['product_id' => 'product_id']
            )
            ->where('customer_id = '. $customerId)
            ->where($this->getMainTable() . '.state IN (?)', [SalesOrder::STATE_COMPLETE]);
        return (bool) $connection->fetchOne($select);
    }
}
