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

use Aheadworks\RewardPoints\Model\PointsSummary as PointsSummaryModel;

/**
 * Class Aheadworks\RewardPoints\Model\ResourceModel\PointsSummary
 */
class PointsSummary extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     *  {@inheritDoc}
     */
    protected function _construct()
    {
        $this->_init('aw_rp_points_summary', 'summary_id');
    }

    /**
     * Load points summary by customer id
     *
     * @param  PointsSummaryModel $pointsSummary
     * @param  int $customerId
     * @return \Aheadworks\RewardPoints\Model\ResourceModel\PointsSummary
     */
    public function loadByCustomerId(PointsSummaryModel $pointsSummary, $customerId)
    {
        return $this->load($pointsSummary, $customerId, PointsSummaryModel::CUSTOMER_ID);
    }

    /**
     * Get id by customer id
     *
     * @param int $customerId
     * @return int
     */
    public function getIdByCustomerId($customerId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from($this->getMainTable(), 'summary_id')
            ->where('customer_id = :customer_id');

        $bind = [':customer_id' => (int)$customerId];

        return $connection->fetchOne($select, $bind);
    }
}
