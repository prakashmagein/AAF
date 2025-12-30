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
use Aheadworks\RewardPoints\Api\SpendRuleManagementInterface;
use Aheadworks\RewardPoints\Api\SpendRuleRepositoryInterface;
use Aheadworks\RewardPoints\Model\DateTime;
use Aheadworks\RewardPoints\Model\Source\SpendRule\Type;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\RewardPoints\Model\SpendRule\Search\Builder as SearchBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Management
 */
class Management implements SpendRuleManagementInterface
{
    /**
     * @var SpendRuleRepositoryInterface
     */
    private $spendRuleRepository;

    /**
     * @var SpendRuleInterfaceFactory
     */
    private $spendRuleFactory;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var SearchBuilder
     */
    private $searchBuilder;

    /**
     * @param SpendRuleRepositoryInterface $spendRuleRepository
     * @param SpendRuleInterfaceFactory $spendRuleFactory
     * @param DateTime $dateTime
     * @param DataObjectHelper $dataObjectHelper
     * @param SearchBuilder $searchBuilder
     */
    public function __construct(
        SpendRuleRepositoryInterface $spendRuleRepository,
        SpendRuleInterfaceFactory $spendRuleFactory,
        DateTime $dateTime,
        DataObjectHelper $dataObjectHelper,
        SearchBuilder $searchBuilder
    ) {
        $this->spendRuleRepository = $spendRuleRepository;
        $this->spendRuleFactory = $spendRuleFactory;
        $this->dateTime = $dateTime;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->searchBuilder = $searchBuilder;
    }

    /**
     * Enable the rule
     *
     * @param int $ruleId
     * @return SpendRuleInterface
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     */
    public function enable($ruleId): SpendRuleInterface
    {
        /** @var SpendRuleInterface $rule */
        $rule = $this->spendRuleRepository->get($ruleId);
        $rule->setStatus(SpendRuleInterface::STATUS_ENABLED);
        $rule = $this->spendRuleRepository->save($rule);

        return $rule;
    }

    /**
     * Disable the rule
     *
     * @param int $ruleId
     * @return SpendRuleInterface
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     */
    public function disable($ruleId): SpendRuleInterface
    {
        /** @var SpendRuleInterface $rule */
        $rule = $this->spendRuleRepository->get($ruleId);
        $rule->setStatus(SpendRuleInterface::STATUS_DISABLED);
        $rule = $this->spendRuleRepository->save($rule);

        return $rule;
    }

    /**
     * Are there enabled rules
     *
     * @param int[] $websiteIds
     * @return bool
     */
    public function areThereEnabledRules(array $websiteIds = []): bool
    {
        try {
            $this->searchBuilder->addStatusFilter(SpendRuleInterface::STATUS_ENABLED);
            if ($websiteIds) {
                $this->searchBuilder->addWebsiteIdsFilterIn($websiteIds);
            }
            $result = (bool)$this->searchBuilder->searchSpendRules();
        } catch (LocalizedException $e) {
            $result = false;
        }
        return $result;
    }

    /**
     * Get active rules
     *
     * @param int[] $websiteIds
     * @param array $excludedRuleIds
     * @return SpendRuleInterface[]
     */
    public function getActiveRules(array $websiteIds = [], array $excludedRuleIds = []): array
    {
        $todayDate = $this->dateTime->getTodayDate();

        try {
            if ($excludedRuleIds) {
                $this->searchBuilder->addIdsFilterNin($excludedRuleIds);
            }
            if ($websiteIds) {
                $this->searchBuilder->addWebsiteIdsFilterIn($websiteIds);
            }
            $this->searchBuilder->addStatusFilter(SpendRuleInterface::STATUS_ENABLED);
            $this->searchBuilder->addDateFilter($todayDate);
            $this->searchBuilder->addSorting(SpendRuleInterface::PRIORITY, SortOrder::SORT_ASC);

            $rules = $this->searchBuilder->searchSpendRules();
        } catch (LocalizedException $e) {
            $rules = [];
        }
        return $rules;
    }

    /**
     * Get rules by ids
     *
     * @param array|null $ruleIds
     * @return SpendRuleInterface[]
     */
    public function getRulesByIds(?array $ruleIds): array
    {
        try {
            $this->searchBuilder->addIdsFilterIn($ruleIds);
            $this->searchBuilder->addSorting(SpendRuleInterface::PRIORITY, SortOrder::SORT_ASC);
            $rules = $this->searchBuilder->searchSpendRules();
        } catch (LocalizedException $e) {
            $rules = [];
        }
        return $rules;
    }

    /**
     * Get active rules for indexer
     *
     * @return SpendRuleInterface[]
     */
    public function getActiveRulesForIndexer(): array
    {
        $todayDate = $this->dateTime->getTodayDate();

        try {
            $this->searchBuilder->addStatusFilter(SpendRuleInterface::STATUS_ENABLED);
            $this->searchBuilder->addTypeFilter(Type::CATALOG);
            $this->searchBuilder->addDateFilter($todayDate);
            $this->searchBuilder->addSorting(SpendRuleInterface::PRIORITY, SortOrder::SORT_ASC);

            $rules = $this->searchBuilder->searchSpendRules();
        } catch (LocalizedException $e) {
            $rules = [];
        }
        return $rules;
    }
}
