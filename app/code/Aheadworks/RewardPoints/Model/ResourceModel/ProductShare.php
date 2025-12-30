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

use Aheadworks\RewardPoints\Model\ProductShare as ProductShareModel;

/**
 * Class Aheadworks\RewardPoints\Model\ResourceModel\ProductShare
 */
class ProductShare extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * {@inheritDoc}
     */
    protected function _construct()
    {
        $this->_init('aw_rp_product_share', 'share_id');
    }

    /**
     * Get share row id by customer id,
     * product id and network
     *
     * @param int $customerId
     * @param int $productId
     * @param string $network
     * @return string|NULL
     */
    public function getShareRowId($customerId, $productId, $network)
    {
        $select = $this->getShareRowSelect($customerId, $productId, $network, 'share_id');
        if ($select instanceof \Magento\Framework\DB\Select) {
            return $this->getConnection()->fetchOne($select);
        }
        return null;
    }

    /**
     * Get select query by customer id,
     * product id and network
     *
     * @param int $customerId
     * @param int $productId
     * @param string $network
     * @param string $cols
     * @return \Magento\Framework\DB\Select
     */
    private function getShareRowSelect($customerId, $productId, $network, $cols = '*')
    {
        $connection = $this->getConnection();

        if ($connection && $customerId !== null
            && $productId !== null && $network !== null
        ) {
            $mainTable = $this->getMainTable();
            $select = $connection->select()->from($mainTable, $cols);

            $customerIdField = $connection->quoteIdentifier(
                sprintf('%s.%s', $mainTable, ProductShareModel::CUSTOMER_ID)
            );
            $productIdField = $connection->quoteIdentifier(
                sprintf('%s.%s', $mainTable, ProductShareModel::PRODUCT_ID)
            );
            $networkField = $connection->quoteIdentifier(
                sprintf('%s.%s', $mainTable, ProductShareModel::NETWORK)
            );

            $select->where($customerIdField . '=?', $customerId);
            $select->where($productIdField . '=?', $productId);
            $select->where($networkField . '=?', $network);

            return $select;
        }
        return null;
    }
}
