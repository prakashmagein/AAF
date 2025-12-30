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

namespace Aheadworks\RewardPoints\Block\Adminhtml\SpendRule\Edit\Conditions;

use Aheadworks\RewardPoints\Api\Data\SpendRuleInterface;
use Aheadworks\RewardPoints\Controller\Adminhtml\Spending\Rules\Edit as RuleEditAction;
use Aheadworks\RewardPoints\Api\Data\SpendRuleInterfaceFactory;
use Aheadworks\RewardPoints\Ui\DataProvider\SpendRule\FormDataProvider as SpendRuleFormDataProvider;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Registry;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class DataProvider
 */
class DataProvider
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var SpendRuleInterfaceFactory
     */
    private $ruleFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var TypeResolver
     */
    private $typeResolver;

    /**
     * @param DataPersistorInterface $dataPersistor
     * @param DataObjectProcessor $dataObjectProcessor
     * @param Registry $coreRegistry
     * @param SpendRuleInterfaceFactory $ruleFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param TypeResolver $typeResolver
     */
    public function __construct(
        DataPersistorInterface $dataPersistor,
        DataObjectProcessor $dataObjectProcessor,
        Registry $coreRegistry,
        SpendRuleInterfaceFactory $ruleFactory,
        DataObjectHelper $dataObjectHelper,
        TypeResolver $typeResolver
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->coreRegistry = $coreRegistry;
        $this->ruleFactory = $ruleFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->typeResolver = $typeResolver;
    }

    /**
     * Retrieve condition data array from the rule
     *
     * @param string $conditionType
     * @return array|null
     */
    public function getConditions(string $conditionType): ?array
    {
        $conditions = null;
        $rule = $this->getRule();
        if ($rule
            && $rule->getCondition()
            && in_array($rule->getType(), $this->typeResolver->resolve($conditionType))
        ) {
            $ruleData = $this->dataObjectProcessor->buildOutputDataArray(
                $rule,
                SpendRuleInterface::class
            );
            $conditions = $ruleData[SpendRuleInterface::CONDITION];
        }
        return $conditions;
    }

    /**
     * Retrieve rule model
     *
     * @return SpendRuleInterface|null
     */
    public function getRule(): ?SpendRuleInterface
    {
        $persistedRule = $this->dataPersistor->get(SpendRuleFormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY);
        if (!empty($persistedRule && is_array($persistedRule))) {
            $rule = $this->ruleFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $rule,
                $persistedRule,
                SpendRuleInterface::class
            );
        } else {
            /** @var SpendRuleInterface $rule */
            $rule = $this->coreRegistry->registry(RuleEditAction::CURRENT_RULE_KEY);
        }

        return $rule;
    }
}
