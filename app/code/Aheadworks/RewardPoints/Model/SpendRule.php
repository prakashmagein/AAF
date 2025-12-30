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

namespace Aheadworks\RewardPoints\Model;

use Aheadworks\RewardPoints\Api\Data\ActionInterface;
use Aheadworks\RewardPoints\Api\Data\SpendRuleInterface;
use Aheadworks\RewardPoints\Model\ResourceModel\SpendRule as SpendRuleResource;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Aheadworks\RewardPoints\Api\Data\ConditionInterface;
use Magento\Framework\Validator\Exception;
use Aheadworks\RewardPoints\Api\Data\SpendRuleExtensionInterface;

/**
 * Class SpendRule
 */
class SpendRule extends AbstractModel implements SpendRuleInterface
{
    /**
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Model construct that should be used for object initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(SpendRuleResource::class);
    }

    /**
     * Get rule id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Set rule id
     *
     * @param int $ruleId
     * @return $this
     */
    public function setId($ruleId)
    {
        return $this->setData(self::ID, $ruleId);
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Get description
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * Set description
     *
     * @param string|null $description
     * @return $this
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Get "from" date
     *
     * @return string|null
     */
    public function getFromDate()
    {
        return $this->getData(self::FROM_DATE);
    }

    /**
     * Set "from" date
     *
     * @param string|null $fromDate
     * @return $this
     */
    public function setFromDate($fromDate)
    {
        return $this->setData(self::FROM_DATE, $fromDate);
    }

    /**
     * Get "to" date
     *
     * @return string|null
     */
    public function getToDate()
    {
        return $this->getData(self::TO_DATE);
    }

    /**
     * Set "to" date
     *
     * @param string|null $toDate
     * @return $this
     */
    public function setToDate($toDate)
    {
        return $this->setData(self::TO_DATE, $toDate);
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Set status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get type
     *
     * @return string|null
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType(string $type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * Get priority
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->getData(self::PRIORITY);
    }

    /**
     * Set priority
     *
     * @param int $priority
     * @return $this
     */
    public function setPriority($priority)
    {
        return $this->setData(self::PRIORITY, $priority);
    }

    /**
     * Get "discard subsequent rules"
     *
     * @return bool
     */
    public function getDiscardSubsequentRules()
    {
        return $this->getData(self::DISCARD_SUBSEQUENT_RULES);
    }

    /**
     * Set "discard subsequent rules"
     *
     * @param bool $discardSubsequentRules
     * @return $this
     */
    public function setDiscardSubsequentRules($discardSubsequentRules)
    {
        return $this->setData(self::DISCARD_SUBSEQUENT_RULES, $discardSubsequentRules);
    }

    /**
     * Get website ids
     *
     * @return int[]
     */
    public function getWebsiteIds()
    {
        return $this->getData(self::WEBSITE_IDS);
    }

    /**
     * Set website ids
     *
     * @param int[] $websiteIds
     * @return $this
     */
    public function setWebsiteIds($websiteIds)
    {
        return $this->setData(self::WEBSITE_IDS, $websiteIds);
    }

    /**
     * Get customer group ids
     *
     * @return int[]
     */
    public function getCustomerGroupIds()
    {
        return $this->getData(self::CUSTOMER_GROUP_IDS);
    }

    /**
     * Set customer group ids
     *
     * @param int[] $customerGroupIds
     * @return $this
     */
    public function setCustomerGroupIds($customerGroupIds)
    {
        return $this->setData(self::CUSTOMER_GROUP_IDS, $customerGroupIds);
    }

    /**
     * Get condition
     *
     * @return ConditionInterface
     */
    public function getCondition()
    {
        return $this->getData(self::CONDITION);
    }

    /**
     * Set condition
     *
     * @param ConditionInterface $condition
     * @return $this
     */
    public function setCondition($condition)
    {
        return $this->setData(self::CONDITION, $condition);
    }

    /**
     * Get action
     *
     * @return ActionInterface[]
     */
    public function getAction()
    {
        return $this->getData(self::ACTION);
    }

    /**
     * Set action
     *
     * @param ActionInterface[] $action
     * @return $this
     */
    public function setAction($action)
    {
        return $this->setData(self::ACTION, $action);
    }

    /**
     * Retrieve ID of entity with storefront labels
     *
     * @return int
     */
    public function getEntityId()
    {
        return $this->getId();
    }

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return SpendRuleExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * Set an extension attributes object
     *
     * @param SpendRuleExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        SpendRuleExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }

    /**
     * Validate entity
     *
     * @return $this
     * @throws Exception
     */
    public function validate()
    {
        return $this->validateBeforeSave();
    }
}
