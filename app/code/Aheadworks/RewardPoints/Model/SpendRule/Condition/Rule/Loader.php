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

namespace Aheadworks\RewardPoints\Model\SpendRule\Condition\Rule;

use Aheadworks\RewardPoints\Api\Data\SpendRuleInterface;
use Aheadworks\RewardPoints\Model\SpendRule\Condition\Rule\CatalogFactory as CatalogRuleFactory;
use Aheadworks\RewardPoints\Model\SpendRule\Condition\Rule\Catalog as CatalogRule;
use Aheadworks\RewardPoints\Model\SpendRule\Condition\Rule as ConditionRule;
use Aheadworks\RewardPoints\Model\SpendRule\Condition\RuleFactory as ConditionRuleFactory;
use Aheadworks\RewardPoints\Model\SpendRule\Condition\Converter as ConditionConverter;
use Aheadworks\RewardPoints\Api\Data\ConditionInterface;

/**
 * Class Loader
 */
class Loader
{
    /**
     * @var CatalogRule[]
     */
    private $catalogRules = [];

    /**
     * @var CatalogRuleFactory
     */
    private $catalogRuleFactory;

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
     * @param CatalogRuleFactory $catalogRuleFactory
     */
    public function __construct(
        ConditionConverter $conditionConverter,
        ConditionRuleFactory $conditionRuleFactory,
        CatalogRuleFactory $catalogRuleFactory
    ) {
        $this->conditionConverter = $conditionConverter;
        $this->conditionRuleFactory = $conditionRuleFactory;
        $this->catalogRuleFactory = $catalogRuleFactory;
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
     * @param SpendRuleInterface $rule
     * @return CatalogRule
     */
    public function loadCondition($rule)
    {
        if (!isset($this->catalogRules[$rule->getId()])) {
            $catalogRule = $this->catalogRuleFactory->create();
            if ($conditions = $rule->getCondition()) {
                $conditionArray = $this->conditionConverter->dataModelToArray($conditions);
                $catalogRule->setConditions([])
                    ->getConditions()
                    ->loadArray($conditionArray);
            } else {
                $catalogRule->setConditions([])
                    ->getConditions()
                    ->asArray();
            }
            $this->catalogRules[$rule->getId()] = $catalogRule;
        }

        return $this->catalogRules[$rule->getId()];
    }
}
