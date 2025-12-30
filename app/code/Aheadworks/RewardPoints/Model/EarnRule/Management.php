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
namespace Aheadworks\RewardPoints\Model\EarnRule;

use Aheadworks\RewardPoints\Api\Data\EarnRuleInterface;
use Aheadworks\RewardPoints\Api\Data\EarnRuleInterfaceFactory;
use Aheadworks\RewardPoints\Api\EarnRuleManagementInterface;
use Aheadworks\RewardPoints\Api\EarnRuleRepositoryInterface;
use Aheadworks\RewardPoints\Model\DateTime;
use Aheadworks\RewardPoints\Model\Source\EarnRule\Type;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\RewardPoints\Model\EarnRule\Search\Builder as SearchBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Management
 */
class Management implements EarnRuleManagementInterface
{
    /**
     * @var EarnRuleRepositoryInterface
     */
    private $earnRuleRepository;

    /**
     * @var EarnRuleInterfaceFactory
     */
    private $earnRuleFactory;

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
     * @param EarnRuleRepositoryInterface $earnRuleRepository
     * @param EarnRuleInterfaceFactory $earnRuleFactory
     * @param DateTime $dateTime
     * @param DataObjectHelper $dataObjectHelper
     * @param SearchBuilder $searchBuilder
     */
    public function __construct(
        EarnRuleRepositoryInterface $earnRuleRepository,
        EarnRuleInterfaceFactory $earnRuleFactory,
        DateTime $dateTime,
        DataObjectHelper $dataObjectHelper,
        SearchBuilder $searchBuilder
    ) {
        $this->earnRuleRepository = $earnRuleRepository;
        $this->earnRuleFactory = $earnRuleFactory;
        $this->dateTime = $dateTime;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->searchBuilder = $searchBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function enable($ruleId)
    {
        /** @var EarnRuleInterface $rule */
        $rule = $this->earnRuleRepository->get($ruleId);
        $rule->setStatus(EarnRuleInterface::STATUS_ENABLED);
        $rule = $this->earnRuleRepository->save($rule);

        return $rule;
    }

    /**
     * {@inheritdoc}
     */
    public function disable($ruleId)
    {
        /** @var EarnRuleInterface $rule */
        $rule = $this->earnRuleRepository->get($ruleId);
        $rule->setStatus(EarnRuleInterface::STATUS_DISABLED);
        $rule = $this->earnRuleRepository->save($rule);

        return $rule;
    }

    /**
     * {@inheritdoc}
     */
    public function createRule($ruleData)
    {
        /** @var EarnRuleInterface $rule */
        $rule = $this->earnRuleFactory->create();

        $this->dataObjectHelper->populateWithArray($rule, $ruleData, EarnRuleInterface::class);
        $rule = $this->earnRuleRepository->save($rule);

        return $rule;
    }

    /**
     * {@inheritdoc}
     */
    public function updateRule($ruleId, $ruleData)
    {
        /** @var EarnRuleInterface $rule */
        $rule = $this->earnRuleRepository->get($ruleId);

        $this->dataObjectHelper->populateWithArray($rule, $ruleData, EarnRuleInterface::class);
        $rule = $this->earnRuleRepository->save($rule);

        return $rule;
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveRules($excludedRuleIds = null)
    {
        $todayDate = $this->dateTime->getTodayDate();

        try {
            if ($excludedRuleIds) {
                $this->searchBuilder->addIdsFilterNin($excludedRuleIds);
            }
            $this->searchBuilder->addStatusFilter(EarnRuleInterface::STATUS_ENABLED);
            $this->searchBuilder->addDateFilter($todayDate);
            $this->searchBuilder->addSorting(EarnRuleInterface::PRIORITY, SortOrder::SORT_ASC);

            $rules = $this->searchBuilder->searchEarnRules();
        } catch (LocalizedException $e) {
            $rules = [];
        }
        return $rules;
    }

    public function getRulesByIds($ruleIds)
    {
        try {
            $this->searchBuilder->addIdsFilterIn($ruleIds);
            $this->searchBuilder->addSorting(EarnRuleInterface::PRIORITY, SortOrder::SORT_ASC);
            $rules = $this->searchBuilder->searchEarnRules();
        } catch (LocalizedException $e) {
            $rules = [];
        }
        return $rules;
    }

    public function getActiveRulesForIndexer()
    {
        $todayDate = $this->dateTime->getTodayDate();

        try {
            $this->searchBuilder->addStatusFilter(EarnRuleInterface::STATUS_ENABLED);
            $this->searchBuilder->addTypeFilter(Type::CATALOG);
            $this->searchBuilder->addDateFilter($todayDate);
            $this->searchBuilder->addSorting(EarnRuleInterface::PRIORITY, SortOrder::SORT_ASC);

            $rules = $this->searchBuilder->searchEarnRules();
        } catch (LocalizedException $e) {
            $rules = [];
        }
        return $rules;
    }
}
