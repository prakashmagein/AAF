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
namespace Aheadworks\RewardPoints\Api;

use Aheadworks\RewardPoints\Api\Data\SpendRuleInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\RewardPoints\Api\Data\SpendRuleSearchResultsInterface;

/**
 * Interface SpendRuleRepositoryInterface
 * @api
 */
interface SpendRuleRepositoryInterface
{
    /**
     * Save rule
     *
     * @param SpendRuleInterface $rule
     * @return SpendRuleInterface
     * @throws CouldNotSaveException If validation fails
     */
    public function save(SpendRuleInterface $rule): SpendRuleInterface;

    /**
     * Retrieve rule with storefront labels for specified store view
     *
     * @param int $ruleId
     * @param int|null $storeId
     * @return SpendRuleInterface
     * @throws NoSuchEntityException If the rule does not exist
     */
    public function get($ruleId, $storeId = null): SpendRuleInterface;

    /**
     * Retrieve rules matching the specified criteria with storefront labels for specified store view
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param int|null $storeId
     * @return SpendRuleSearchResultsInterface
     * @throws LocalizedException if an error occurs
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $storeId = null);

    /**
     * Delete rule
     *
     * @param SpendRuleInterface $rule
     * @return bool true on success
     * @throws NoSuchEntityException If the rule does not exist
     */
    public function delete(SpendRuleInterface $rule): bool;

    /**
     * Delete rule by id
     *
     * @param int $ruleId
     * @return bool true on success
     * @throws NoSuchEntityException If the rule does not exist
     */
    public function deleteById($ruleId): bool;
}
