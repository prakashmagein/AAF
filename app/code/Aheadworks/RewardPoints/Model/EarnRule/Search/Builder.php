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

namespace Aheadworks\RewardPoints\Model\EarnRule\Search;

use Aheadworks\RewardPoints\Api\EarnRuleRepositoryInterface;
use Aheadworks\RewardPoints\Api\Data\EarnRuleInterface;
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
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SortOrderFactory
     */
    private $sortOrderFactory;

    /**
     * @var EarnRuleRepositoryInterface
     */
    private $earnRuleRepository;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderFactory $sortOrderFactory
     * @param EarnRuleRepositoryInterface $earnRuleRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderFactory $sortOrderFactory,
        EarnRuleRepositoryInterface $earnRuleRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderFactory = $sortOrderFactory;
        $this->earnRuleRepository = $earnRuleRepository;
    }

    /**
     * Search earn rule to prepared search criteria
     *
     * @return EarnRuleInterface[]
     * @throws LocalizedException
     */
    public function searchEarnRules(): array
    {
        $searchResults = $this->earnRuleRepository
            ->getList($this->buildSearchCriteria());

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
        $this->getSearchCriteriaBuilder()->addFilter(EarnRuleInterface::TO_DATE, $todayDate);
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
        $this->getSearchCriteriaBuilder()->addFilter(EarnRuleInterface::STATUS, $status, 'eq');
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
        $this->getSearchCriteriaBuilder()->addFilter(EarnRuleInterface::ID, $ids, 'in');
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
        $this->getSearchCriteriaBuilder()->addFilter(EarnRuleInterface::ID, $ids, 'nin');
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
        $this->getSearchCriteriaBuilder()->addFilter(EarnRuleInterface::TYPE, $type, 'eq');
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
