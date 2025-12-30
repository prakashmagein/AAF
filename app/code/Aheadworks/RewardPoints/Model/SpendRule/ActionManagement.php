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

namespace Aheadworks\RewardPoints\Model\SpendRule;

use Aheadworks\RewardPoints\Api\Data\SpendRuleInterface;
use Aheadworks\RewardPoints\Api\Data\SpendRuleInterfaceFactory;
use Aheadworks\RewardPoints\Api\SpendRuleRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class ActionManagement
 */
class ActionManagement
{
    /**
     * ActionManagement constructor.
     *
     * @param SpendRuleRepositoryInterface $spendRuleRepository
     * @param SpendRuleInterfaceFactory $spendRuleFactory
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        private SpendRuleRepositoryInterface $spendRuleRepository,
        private SpendRuleInterfaceFactory $spendRuleFactory,
        private DataObjectHelper $dataObjectHelper
    ) {
    }

    /**
     * Create the rule
     *
     * @param array $ruleData
     * @return SpendRuleInterface
     * @throws CouldNotSaveException
     */
    public function createRule(array $ruleData): SpendRuleInterface
    {
        /** @var SpendRuleInterface $rule */
        $rule = $this->spendRuleFactory->create();

        $this->dataObjectHelper->populateWithArray($rule, $ruleData, SpendRuleInterface::class);
        $rule = $this->spendRuleRepository->save($rule);

        return $rule;
    }

    /**
     * Update the rule
     *
     * @param int $ruleId
     * @param array $ruleData
     * @return SpendRuleInterface
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     */
    public function updateRule($ruleId, array $ruleData): SpendRuleInterface
    {
        /** @var SpendRuleInterface $rule */
        $rule = $this->spendRuleRepository->get($ruleId);

        $this->dataObjectHelper->populateWithArray($rule, $ruleData, SpendRuleInterface::class);
        $rule = $this->spendRuleRepository->save($rule);

        return $rule;
    }
}
