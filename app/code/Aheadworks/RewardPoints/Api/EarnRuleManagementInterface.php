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

namespace Aheadworks\RewardPoints\Api;

use Aheadworks\RewardPoints\Api\Data\EarnRuleInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class EarnRuleManagementInterface
 * @package Aheadworks\RewardPoints\Api
 * @api
 */
interface EarnRuleManagementInterface
{
    /**
     * Enable the rule
     *
     * @param int $ruleId
     * @return EarnRuleInterface
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     */
    public function enable($ruleId);

    /**
     * Disable the rule
     *
     * @param int $ruleId
     * @return EarnRuleInterface
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     */
    public function disable($ruleId);

    /**
     * Create the rule
     *
     * @param array $ruleData
     * @return EarnRuleInterface
     * @throws CouldNotSaveException
     */
    public function createRule($ruleData);

    /**
     * Update the rule
     *
     * @param int $ruleId
     * @param array $ruleData
     * @return EarnRuleInterface
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     */
    public function updateRule($ruleId, $ruleData);

    /**
     * Get active rules
     *
     * @param array $excludedRuleIds
     * @return EarnRuleInterface[]
     */
    public function getActiveRules(array $excludedRuleIds);

    /**
     * Get rules by ids
     *
     * @param array|null $ruleIds
     * @return EarnRuleInterface[]
     */
    public function getRulesByIds(?array $ruleIds);

    /**
     * Get active rules for indexer
     *
     * @return EarnRuleInterface[]
     */
    public function getActiveRulesForIndexer();
}
