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
namespace Aheadworks\RewardPoints\Model\EarnRule\Condition\Rule;

use Aheadworks\RewardPoints\Api\Data\EarnRuleInterface;
use Aheadworks\RewardPoints\Model\EarnRule\Condition\Rule\CartFactory as CartRuleFactory;
use Aheadworks\RewardPoints\Model\EarnRule\Condition\Rule\Cart as CartRule;
use Aheadworks\RewardPoints\Model\EarnRule\Condition\Rule\Catalog as CatalogRule;
use Aheadworks\RewardPoints\Model\EarnRule\Condition\Rule as ConditionRule;
use Aheadworks\RewardPoints\Model\EarnRule\Condition\RuleFactory as ConditionRuleFactory;
use Aheadworks\RewardPoints\Model\EarnRule\Condition\Converter as ConditionConverter;
use Aheadworks\RewardPoints\Api\Data\ConditionInterface;

/**
 * Class Loader
 */
class Loader
{
    /**
     * @var CartRule
     */
    private $cartRules = [];

    /**
     * @var CartRuleFactory
     */
    private $cartRuleFactory;

    /**
     * @var ConditionRuleFactory
     */
    private $conditionRuleFactory;

    /**
     * @var ConditionConverter
     */
    private $conditionConverter;

    /**
     * @param ConditionConverter $conditionConverter
     * @param ConditionRuleFactory $conditionRuleFactory
     * @param CartRuleFactory $cartRuleFactory
     */
    public function __construct(
        ConditionConverter $conditionConverter,
        ConditionRuleFactory $conditionRuleFactory,
        CartRuleFactory $cartRuleFactory
    ) {
        $this->conditionConverter = $conditionConverter;
        $this->conditionRuleFactory = $conditionRuleFactory;
        $this->cartRuleFactory = $cartRuleFactory;
    }

    /**
     * Create condition rule by corresponding condition object
     *
     * @param ConditionInterface|null $condition
     * @return ConditionRule
     */
    public function loadRule($condition = null)
    {
        /** @var ConditionRule $conditionRule */
        $conditionRule = $this->conditionRuleFactory->create();
        if (empty($condition)) {
            $conditionRule->setConditions([])
                ->getConditions()
                ->asArray();
        } else {
            $conditionArray = $this->conditionConverter->dataModelToArray($condition);
            $conditionRule->setConditions([])
                ->getConditions()
                ->loadArray($conditionArray);
        }

        return $conditionRule;
    }

    /**
     * Load cart conditions by rule
     *
     * @param EarnRuleInterface $rule
     * @return CartRule|CatalogRule
     */
    public function loadCondition($rule)
    {
        if (!isset($this->cartRules[$rule->getId()])) {
            $cartRule = $this->cartRuleFactory->create();
            if ($conditions = $rule->getCondition()) {
                $conditionArray = $this->conditionConverter->dataModelToArray($conditions);
                $cartRule->setConditions([])
                    ->getConditions()
                    ->loadArray($conditionArray);
            } else {
                $cartRule->setConditions([])
                    ->getConditions()
                    ->asArray();
            }
            $this->cartRules[$rule->getId()] = $cartRule;
        }

        return $this->cartRules[$rule->getId()];
    }
}
