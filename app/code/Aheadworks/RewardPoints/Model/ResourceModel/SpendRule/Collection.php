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

namespace Aheadworks\RewardPoints\Model\ResourceModel\SpendRule;

use Aheadworks\RewardPoints\Model\ResourceModel\AbstractCollection as BaseAbstractCollection;
use Aheadworks\RewardPoints\Api\Data\SpendRuleInterface;
use Aheadworks\RewardPoints\Model\SpendRule;
use Aheadworks\RewardPoints\Model\ResourceModel\SpendRule as SpendRuleResource;

/**
 * Class Collection
 */
class Collection extends BaseAbstractCollection
{
    /**
     * Identifier field name for collection items
     *
     * Can be used by collections with items without defined
     *
     * @var string
     */
    protected $_idFieldName = SpendRuleResource::MAIN_TABLE_ID_FIELD_NAME;

    /**
     * @var array
     */
    private array $publicFilterFields = [
        SpendRuleInterface::CUSTOMER_GROUP_IDS,
        SpendRuleInterface::WEBSITE_IDS
    ];

    /**
     * Initialization here
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(SpendRule::class, SpendRuleResource::class);
    }

    /**
     * Redeclare after load method for specifying collection items original data
     *
     * @return $this
     */
    protected function _afterLoad(): self
    {
        $this->attachRelationTable(
            SpendRuleResource::CUSTOMER_GROUP_TABLE_NAME,
            'id',
            'rule_id',
            'customer_group_id',
            'customer_group_ids',
            [],
            [],
            true
        );
        $this->attachRelationTable(
            SpendRuleResource::WEBSITE_TABLE_NAME,
            'id',
            'rule_id',
            'website_id',
            'website_ids',
            [],
            [],
            true
        );
        return parent::_afterLoad();
    }

    /**
     * Hook for operations before rendering filters
     *
     * @return void
     */
    protected function _renderFiltersBefore(): void
    {
        $this->joinLinkageTable(
            SpendRuleResource::CUSTOMER_GROUP_TABLE_NAME,
            'id',
            'rule_id',
            'customer_group_ids',
            'customer_group_id'
        );
        $this->joinLinkageTable(
            SpendRuleResource::WEBSITE_TABLE_NAME,
            'id',
            'rule_id',
            'website_ids',
            'website_id'
        );
        parent::_renderFiltersBefore();
    }

    /**
     * Add field filter to collection
     *
     * @param string|array $field
     * @param null|string|array $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        $fieldsToProcess = $this->processAddFieldToFilter($field, $condition);

        if (!empty($fieldsToProcess)) {
            return parent::addFieldToFilter($fieldsToProcess, $condition);
        }

        return $this;
    }

    /**
     * Process adding fields to filter
     *
     * @param string|array $field
     * @param null|string|array $condition
     * @return array|string
     */
    private function processAddFieldToFilter($field, $condition = null)
    {
        $fieldsToProcess = null;
        if (is_array($field)) {
            $fieldsToProcess = [];
            foreach ($field as $fieldName) {
                if ($this->isPublicFilter($fieldName)) {
                    $this->addFilter($fieldName, $condition, 'public');
                } elseif ($fieldName == SpendRuleInterface::FROM_DATE) {
                    $this->addFromDateFilter($condition);
                } elseif ($fieldName == SpendRuleInterface::TO_DATE) {
                    $this->addToDateFilter($condition);
                } else {
                    $fieldsToProcess[] = $fieldName;
                }
            }
        } else {
            if ($this->isPublicFilter($field)) {
                $this->addFilter($field, $condition, 'public');
            } else {
                $fieldsToProcess = $field;
            }
        }

        return $fieldsToProcess;
    }

    /**
     * Check if need to apply public filter instead of native logic
     *
     * @param string $fieldName
     * @return bool
     */
    private function isPublicFilter(string $fieldName): bool
    {
        return (in_array($fieldName, $this->publicFilterFields));
    }

    /**
     * Add 'FROM' date filter
     *
     * @param string $fromDate
     * @return $this
     */
    public function addFromDateFilter($fromDate)
    {
        $fromDateField = 'main_table.' . SpendRuleInterface::FROM_DATE;
        $fromCondition = '(' . $fromDateField . ' IS NULL OR ' . $fromDateField . '<= ?)';
        $this->getSelect()->where($fromCondition, $fromDate);

        return $this;
    }

    /**
     * Add 'TO' date filter
     *
     * @param string $toDate
     * @return $this
     */
    public function addToDateFilter($toDate)
    {
        $toDateField = 'main_table.' . SpendRuleInterface::TO_DATE;
        $toCondition = '(' . $toDateField . ' IS NULL OR ' . $toDateField . '>= ?)';
        $this->getSelect()->where($toCondition, $toDate);

        return $this;
    }
}
