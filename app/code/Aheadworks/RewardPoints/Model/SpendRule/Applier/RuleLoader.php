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

namespace Aheadworks\RewardPoints\Model\SpendRule\Applier;

use Aheadworks\RewardPoints\Api\Data\SpendRuleInterface;
use Aheadworks\RewardPoints\Api\Data\SpendRuleSearchResultsInterface;
use Aheadworks\RewardPoints\Api\SpendRuleRepositoryInterface;
use Aheadworks\RewardPoints\Model\ResourceModel\SpendRule as SpendRuleResource;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class RuleLoader
 */
class RuleLoader
{
    /**
     * RuleLoader constructor.
     *
     * @param SpendRuleResource $spendRuleResource
     * @param SpendRuleRepositoryInterface $spendRuleRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        private SpendRuleResource $spendRuleResource,
        private SpendRuleRepositoryInterface $spendRuleRepository,
        private SearchCriteriaBuilder $searchCriteriaBuilder,
        private SortOrderBuilder $sortOrderBuilder
    ) {
    }

    /**
     * Get rules for apply
     *
     * @param int $productId
     * @param int $customerGroupId
     * @param int $websiteId
     * @param string $currentDate
     * @return SpendRuleInterface[]
     */
    public function getRulesForApply(int $productId, int $customerGroupId, int $websiteId, string $currentDate): array
    {
        $ruleIds = $this->spendRuleResource->getRuleIdsToApply($productId, $customerGroupId, $websiteId, $currentDate);

        $orderByPriority = $this->sortOrderBuilder
            ->setField(SpendRuleInterface::PRIORITY)
            ->setAscendingDirection()
            ->create();

        $orderById = $this->sortOrderBuilder
            ->setField(SpendRuleInterface::ID)
            ->setAscendingDirection()
            ->create();

        $this->searchCriteriaBuilder
            ->addFilter(SpendRuleInterface::ID, $ruleIds, 'in')
            ->setSortOrders([$orderByPriority, $orderById]);

        try {
            /** @var SpendRuleSearchResultsInterface $result */
            $result = $this->spendRuleRepository->getList($this->searchCriteriaBuilder->create());
            $rules = $result->getItems();
        } catch (LocalizedException $e) {
            $rules = [];
        }

        return $rules;
    }
}
