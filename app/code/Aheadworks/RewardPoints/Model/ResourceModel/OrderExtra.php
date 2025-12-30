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


use Aheadworks\RewardPoints\Model\OrderExtraInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Class Aheadworks\RewardPoints\Model\ResourceModel\OrderExtra
 */
class OrderExtra extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**#@+
     * Constants defined for tables
     */
    const MAIN_TABLE_NAME   = 'aw_rp_order_additional';
    /**#@-*/

    /**
     *  {@inheritDoc}
     */
    protected function _construct()
    {
        $this->_init('aw_rp_order_additional', 'order_id');
    }

    /**
     * Get applied rule ids
     *
     * @param int $orderId
     * @return string
     */
    public function getAppliedRuleIds(int $orderId): string
    {
        $connection = $this->getConnection();
        $select = $this->getRulesIds($connection, OrderExtraInterface::APPLIED_RULE_IDS, $orderId);
        $result = $connection->fetchOne($select);

        return !$result ? '' : $result;
    }

    /**
     * Get canceled rule ids
     *
     * @param int $orderId
     * @return string
     */
    public function getCanceledRuleIds(?int $orderId): string
    {
        if (!$orderId) {
            return '';
        }
        $connection = $this->getConnection();
        $select = $this->getRulesIds($connection, OrderExtraInterface::CANCELED_RULE_IDS, $orderId);
        $result = $connection->fetchOne($select);

        return !$result ? '' : $result;
    }

    /**
     * Insert or update applied rule ids
     *
     * @param int $orderId
     * @param string $ruleIds
     * @return $this
     */
    public function insertAppliedRuleIds(int $orderId, string $ruleIds): self
    {
        $connection = $this->getConnection();
        if ($this->hasOrderId($orderId)) {
            $this->updateRuleIds($connection, OrderExtraInterface::APPLIED_RULE_IDS, $orderId, $ruleIds);

            return $this;
        }
        $this->insertRuleIds($connection, OrderExtraInterface::APPLIED_RULE_IDS, $orderId, $ruleIds);

        return $this;
    }

    /**
     * Insert or update canceled rule ids
     *
     * @param int $orderId
     * @param string $ruleIds
     * @return $this
     */
    public function insertCanceledRuleIds(int $orderId, string $ruleIds): self
    {
        $connection = $this->getConnection();
        if ($this->hasOrderId($orderId)) {
            $this->updateRuleIds($connection, OrderExtraInterface::CANCELED_RULE_IDS, $orderId, $ruleIds);

            return $this;
        }
        $this->insertRuleIds($connection, OrderExtraInterface::CANCELED_RULE_IDS, $orderId, $ruleIds);

        return $this;
    }

    /**
     * Has order_id in the table
     *
     * @param int $orderId
     * @return string
     */
    public function hasOrderId(int $orderId): string
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(
                $this->getTable(self::MAIN_TABLE_NAME),
                [OrderExtraInterface::ORDER_ID]
            )
            ->where(OrderExtraInterface::ORDER_ID . ' = ?', $orderId);

        return $connection->fetchOne($select);
    }

    /**
     * Get rule ids by type
     *
     * @param AdapterInterface $connection
     * @param string $ruleType
     * @param int $orderId
     * @return string
     */
    private function getRulesIds(AdapterInterface $connection, string $ruleType, int $orderId): string
    {
        $select = $connection->select()
            ->from(
                $this->getTable(self::MAIN_TABLE_NAME),
                [$ruleType]
            )
            ->where(OrderExtraInterface::ORDER_ID . ' = ?', $orderId);

        return $select;
    }

    /**
     * Insert rule ids by type
     *
     * @param AdapterInterface $connection
     * @param string $type
     * @param int $orderId
     * @param string $ruleIds
     */
    private function insertRuleIds(AdapterInterface $connection, string $type, int $orderId, string $ruleIds): void
    {
        $type === OrderExtraInterface::APPLIED_RULE_IDS ?
            $data = [
                OrderExtraInterface::ORDER_ID => $orderId,
                OrderExtraInterface::APPLIED_RULE_IDS => $ruleIds,
                OrderExtraInterface::CANCELED_RULE_IDS => $this->getCanceledRuleIds($orderId)
            ]
            :
            $data = [
                OrderExtraInterface::ORDER_ID => $orderId,
                OrderExtraInterface::APPLIED_RULE_IDS => $this->getAppliedRuleIds($orderId),
                OrderExtraInterface::CANCELED_RULE_IDS => $ruleIds
            ];
        $connection->insert($this->getTable(self::MAIN_TABLE_NAME), $data);
    }

    /**
     * Update rule ids by type
     *
     * @param AdapterInterface $connection
     * @param string $type
     * @param int $orderId
     * @param string $ruleIds
     */
    private function updateRuleIds(AdapterInterface $connection, string $type, int $orderId, string $ruleIds): void
    {
        $ids = $type === OrderExtraInterface::APPLIED_RULE_IDS ? $this->getAppliedRuleIds($orderId) : $this->getCanceledRuleIds($orderId);
        $type === OrderExtraInterface::APPLIED_RULE_IDS ?
            $data = [
                OrderExtraInterface::APPLIED_RULE_IDS => $ids ? $ids . ',' . $ruleIds : $ruleIds,
                OrderExtraInterface::CANCELED_RULE_IDS => $this->getCanceledRuleIds($orderId)
            ]
            :
            $data = [
                OrderExtraInterface::APPLIED_RULE_IDS => $this->getAppliedRuleIds($orderId),
                OrderExtraInterface::CANCELED_RULE_IDS => $ids ? $ids . ',' . $ruleIds : $ruleIds
            ];

        $connection->update($this->getTable(self::MAIN_TABLE_NAME), $data, [OrderExtraInterface::ORDER_ID . ' = ?' => $orderId]);
    }
}
