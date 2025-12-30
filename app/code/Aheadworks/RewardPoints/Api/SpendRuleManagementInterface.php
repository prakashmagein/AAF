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

use Aheadworks\RewardPoints\Api\Data\SpendRuleInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class SpendRuleManagementInterface
 * @api
 */
interface SpendRuleManagementInterface
{
    /**
     * Enable the rule
     *
     * @param int $ruleId
     * @return SpendRuleInterface
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     */
    public function enable($ruleId): SpendRuleInterface;

    /**
     * Disable the rule
     *
     * @param int $ruleId
     * @return SpendRuleInterface
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     */
    public function disable($ruleId): SpendRuleInterface;

    /**
     * Are there enabled rules
     *
     * @param int[] $websiteIds
     * @return bool
     */
    public function areThereEnabledRules(array $websiteIds = []): bool;

    /**
     * Get active rules
     *
     * @param int[] $websiteIds
     * @param array $excludedRuleIds
     * @return SpendRuleInterface[]
     */
    public function getActiveRules(array $websiteIds = [], array $excludedRuleIds = []): array;

    /**
     * Get rules by ids
     *
     * @param int[]|null $ruleIds
     * @return SpendRuleInterface[]
     */
    public function getRulesByIds(?array $ruleIds): array;

    /**
     * Get active rules for indexer
     *
     * @return SpendRuleInterface[]
     */
    public function getActiveRulesForIndexer(): array;
}
