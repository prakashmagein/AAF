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
declare(strict_types=1);

namespace Aheadworks\RewardPoints\Model\ResourceModel;

use Aheadworks\RewardPoints\Model\Indexer\EarnRule\ProductInterface as EarnRuleProductInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class EarnRule
 */
class EarnRule extends AbstractDb
{
    /**#@+
     * Constants defined for tables
     * used by corresponding entity
     */
    const MAIN_TABLE_ID_FIELD_NAME  = 'id';
    const MAIN_TABLE_NAME           = 'aw_rp_earn_rule';
    const WEBSITE_TABLE_NAME        = 'aw_rp_earn_rule_website';
    const CUSTOMER_GROUP_TABLE_NAME = 'aw_rp_earn_rule_customer_group';
    const PRODUCT_TABLE_NAME        = 'aw_rp_earn_rule_product';
    const PRODUCT_IDX_TABLE_NAME    = 'aw_rp_earn_rule_product_idx';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, self::MAIN_TABLE_ID_FIELD_NAME);
    }

    /**
     * Retrieve rule ids to apply
     *
     * @param int $productId
     * @param int $customerGroupId
     * @param int $websiteId
     * @param string $currentDate
     * @return array
     */
    public function getRuleIdsToApply($productId, $customerGroupId, $websiteId, $currentDate)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(
                $this->getTable(self::PRODUCT_TABLE_NAME),
                [EarnRuleProductInterface::RULE_ID]
            )
            ->where(EarnRuleProductInterface::WEBSITE_ID . ' = ?', $websiteId)
            ->where(EarnRuleProductInterface::CUSTOMER_GROUP_ID . ' = ?', $customerGroupId)
            ->where(EarnRuleProductInterface::PRODUCT_ID . ' = ?', $productId)
            ->where(
                'ISNULL(' . EarnRuleProductInterface::FROM_DATE . ') OR '
                . EarnRuleProductInterface::FROM_DATE . ' <= ?',
                $currentDate
            )
            ->where(
                'ISNULL(' . EarnRuleProductInterface::TO_DATE . ') OR '
                . EarnRuleProductInterface::TO_DATE . ' >= ?',
                $currentDate
            )
            ->order(EarnRuleProductInterface::PRIORITY . ' ASC')
            ->order(EarnRuleProductInterface::RULE_ID . ' DESC');

        return $connection->fetchAll($select);
    }

    /**
     * Retrieve product ids by rule to apply
     *
     * @param int $ruleId
     * @param int|null $customerGroupId
     * @param int|null $websiteId
     * @param string $currentDate
     * @return array
     */
    public function getProductIdsByRyleToApply(int $ruleId, ?int $customerGroupId, ?int $websiteId, string $currentDate): array
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(
                $this->getTable(self::PRODUCT_TABLE_NAME),
                [EarnRuleProductInterface::PRODUCT_ID]
            )
            ->where(EarnRuleProductInterface::WEBSITE_ID . ' = ?', $websiteId)
            ->where(EarnRuleProductInterface::CUSTOMER_GROUP_ID . ' = ?', $customerGroupId)
            ->where(EarnRuleProductInterface::RULE_ID . ' = ?', $ruleId)
            ->where(
                'ISNULL(' . EarnRuleProductInterface::FROM_DATE . ') OR '
                . EarnRuleProductInterface::FROM_DATE . ' <= ?',
                $currentDate
            )
            ->where(
                'ISNULL(' . EarnRuleProductInterface::TO_DATE . ') OR '
                . EarnRuleProductInterface::TO_DATE . ' >= ?',
                $currentDate
            )
            ->order(EarnRuleProductInterface::PRIORITY . ' ASC');

        return $connection->fetchCol($select);
    }
}
