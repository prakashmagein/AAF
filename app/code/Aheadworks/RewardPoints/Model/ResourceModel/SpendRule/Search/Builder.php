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

namespace Aheadworks\RewardPoints\Model\SpendRule\Search;

use Aheadworks\RewardPoints\Api\SpendRuleRepositoryInterface;
use Aheadworks\RewardPoints\Api\Data\SpendRuleInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Builder
 */
class Builder
{
    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderFactory $sortOrderFactory
     * @param SpendRuleRepositoryInterface $spendRuleRepository
     */
    public function __construct(
        private SearchCriteriaBuilder $searchCriteriaBuilder,
        private SortOrderFactory $sortOrderFactory,
        private SpendRuleRepositoryInterface $spendRuleRepository
    ) {
    }

    /**
     * Search earn rule to prepared search criteria
     *
     * @return SpendRuleInterface[]
     * @throws LocalizedException
     */
    public function searchEarnRules(): array
    {
        $searchResults = $this->spendRuleRepository->getList($this->buildSearchCriteria());

        return $searchResults->getItems();
    }

    /**
     * Retrieves search criteria builder
     *
     * @return SearchCriteriaBuilder
     */
    public function getSearchCriteriaBuilder(): SearchCriteriaBuilder
    {
        return $this->searchCriteriaBuilder;
    }

    /**
     * Add date filter
     *
     * @param string $todayDate
     * @return $this
     */
    public function addDateFilter(string $todayDate): Builder
    {
        $this->getSearchCriteriaBuilder()->addFilter(SpendRuleInterface::TO_DATE, $todayDate);
        return $this;
    }

    /**
     * Add status filter
     *
     * @param string $status
     * @return $this
     */
    public function addStatusFilter(string $status): Builder
    {
        $this->getSearchCriteriaBuilder()->addFilter(SpendRuleInterface::STATUS, $status, 'eq');
        return $this;
    }

    /**
     * Add ids filter condition type in
     *
     * @param array $ids
     * @return $this
     */
    public function addIdsFilterIn(array $ids): Builder
    {
        $this->getSearchCriteriaBuilder()->addFilter(SpendRuleInterface::ID, $ids, 'in');
        return $this;
    }

    /**
     * Add ids filter condition type in
     *
     * @param array $ids
     * @return $this
     */
    public function addIdsFilterNin(array $ids): Builder
    {
        $this->getSearchCriteriaBuilder()->addFilter(SpendRuleInterface::ID, $ids, 'nin');
        return $this;
    }

    /**
     * Add type filter
     *
     * @param string $type
     * @return $this
     */
    public function addTypeFilter(string $type): Builder
    {
        $this->getSearchCriteriaBuilder()->addFilter(SpendRuleInterface::TYPE, $type, 'eq');
        return $this;
    }

    /**
     * Add sorting
     *
     * @param string $field
     * @param string $direction
     * @return $this
     */
    public function addSorting(string $field, string $direction): Builder
    {
        $sortFilter = $this->sortOrderFactory()
            ->setField($field)
            ->setDirection($direction);
        $this->searchCriteriaBuilder->setSortOrders([$sortFilter]);
        return $this;
    }

    /**
     * Build search criteria
     *
     * @return SearchCriteria
     */
    private function buildSearchCriteria(): SearchCriteria
    {
        return $this->searchCriteriaBuilder->create();
    }

    /**
     * Sort order factory
     *
     * @return SortOrder
     */
    private function sortOrderFactory(): SortOrder
    {
        return $this->sortOrderFactory->create();
    }
}
