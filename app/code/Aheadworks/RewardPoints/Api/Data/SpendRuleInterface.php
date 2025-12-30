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

namespace Aheadworks\RewardPoints\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface SpendRuleInterface
 * @api
 */
interface SpendRuleInterface extends
    ExtensibleDataInterface,
    ValidatableEntityInterface
{
    /**
     * #@+
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const FROM_DATE = 'from_date';
    const TO_DATE = 'to_date';
    const STATUS = 'status';
    const PRIORITY = 'priority';
    const DISCARD_SUBSEQUENT_RULES = 'discard_subsequent_rules';
    const WEBSITE_IDS = 'website_ids';
    const CUSTOMER_GROUP_IDS = 'customer_group_ids';
    const CONDITION = 'condition';
    const ACTION = 'action';
    const TYPE = 'type';
    /**#@-*/

    /**#@+
     * Status values
     */
    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;
    /**#@-*/

    /**
     * Used for saving storefront labels of the entity
     */
    const STOREFRONT_LABELS_ENTITY_TYPE = 'spend_rule';

    /**
     * Get rule id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set rule id
     *
     * @param int $ruleId
     * @return $this
     */
    public function setId($ruleId);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get description
     *
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     *
     * @param string|null $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * Get "from" date
     *
     * @return string|null
     */
    public function getFromDate();

    /**
     * Set "from" date
     *
     * @param string|null $fromDate
     * @return $this
     */
    public function setFromDate($fromDate);

    /**
     * Get "to" date
     *
     * @return string|null
     */
    public function getToDate();

    /**
     * Set "to" date
     *
     * @param string|null $toDate
     * @return $this
     */
    public function setToDate($toDate);

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get type
     *
     * @return string|null
     */
    public function getType();

    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType(string $type);

    /**
     * Get priority
     *
     * @return int
     */
    public function getPriority();

    /**
     * Set priority
     *
     * @param int $priority
     * @return $this
     */
    public function setPriority($priority);

    /**
     * Get "discard subsequent rules"
     *
     * @return bool
     */
    public function getDiscardSubsequentRules();

    /**
     * Set "discard subsequent rules"
     *
     * @param bool $discardSubsequentRules
     * @return $this
     */
    public function setDiscardSubsequentRules($discardSubsequentRules);

    /**
     * Get website ids
     *
     * @return int[]
     */
    public function getWebsiteIds();

    /**
     * Set website ids
     *
     * @param int[] $websiteIds
     * @return $this
     */
    public function setWebsiteIds($websiteIds);

    /**
     * Get customer group ids
     *
     * @return int[]
     */
    public function getCustomerGroupIds();

    /**
     * Set customer group ids
     *
     * @param int[] $customerGroupIds
     * @return $this
     */
    public function setCustomerGroupIds($customerGroupIds);

    /**
     * Get condition
     *
     * @return \Aheadworks\RewardPoints\Api\Data\ConditionInterface
     */
    public function getCondition();

    /**
     * Set condition
     *
     * @param \Aheadworks\RewardPoints\Api\Data\ConditionInterface $condition
     * @return $this
     */
    public function setCondition($condition);

    /**
     * Get action
     *
     * @return \Aheadworks\RewardPoints\Api\Data\ActionInterface[]
     */
    public function getAction();

    /**
     * Set action
     *
     * @param \Aheadworks\RewardPoints\Api\Data\ActionInterface[] $actions
     * @return $this
     */
    public function setAction($action);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\RewardPoints\Api\Data\SpendRuleExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\RewardPoints\Api\Data\SpendRuleExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\RewardPoints\Api\Data\SpendRuleExtensionInterface $extensionAttributes
    );
}
