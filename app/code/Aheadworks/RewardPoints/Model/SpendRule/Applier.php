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
use Aheadworks\RewardPoints\Model\Calculator\ResultInterface;
use Aheadworks\RewardPoints\Model\Calculator\ResultInterfaceFactory;
use Aheadworks\RewardPoints\Model\Calculator\Spending\SpendItemInterface;
use Aheadworks\RewardPoints\Model\DateTime;
use Aheadworks\RewardPoints\Model\SpendRule\Applier\ActionApplier;
use Aheadworks\RewardPoints\Model\SpendRule\Applier\RuleLoader;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Applier
 */
class Applier
{
    /**
     * Applier constructor.
     *
     * @param RuleLoader $ruleLoader
     * @param ActionApplier $actionApplier
     * @param CustomerRepositoryInterface $customerRepository
     * @param ResultInterfaceFactory $resultFactory
     * @param DateTime $dateTime
     */
    public function __construct(
        private RuleLoader $ruleLoader,
        private ActionApplier $actionApplier,
        private CustomerRepositoryInterface $customerRepository,
        private ResultInterfaceFactory $resultFactory,
        private DateTime $dateTime
    ) {
    }

    /**
     * Apply spending rule
     *
     * @param int|null $customerId
     * @param int|null $customerGroupId
     * @param int|null $websiteId
     * @param SpendRuleInterface $rule
     * @param SpendItemInterface $spendItem
     * @return SpendItemInterface
     */
    public function apply(
        ?int $customerId,
        ?int $customerGroupId,
        ?int $websiteId,
        SpendRuleInterface $rule,
        SpendItemInterface $spendItem
    ): SpendItemInterface {
        try {
            if ($customerId && !$customerGroupId) {
                /** @var CustomerInterface $customer */
                $customer = $this->customerRepository->getById($customerId);
                $customerGroupId = (int)$customer->getGroupId();
            }

            if (in_array($customerGroupId, $rule->getCustomerGroupIds()) &&
                in_array($websiteId, $rule->getWebsiteIds())) {
                $applierResult = $this->actionApplier->apply(
                    $rule->getAction(),
                    $customerId,
                    $websiteId,
                    $spendItem
                );
                if ($applierResult) {
                    $appliedRuleIds = $spendItem->getAppliedRuleIds() ?? [];
                    $appliedRuleIds[] = $rule->getId();
                    $spendItem->setAppliedRuleIds(array_unique($appliedRuleIds));
                }
            }

            $result = $spendItem;
        } catch (LocalizedException $e) {
            $result = $spendItem;
        }

        return $result;
    }

    /**
     * Get applied rule ids
     *
     * @param int $productId
     * @param int $customerGroupId
     * @param int $websiteId
     * @return int[]
     */
    public function getAppliedRuleIds(int $productId, int $customerGroupId, int $websiteId): array
    {
        $appliedRuleIds = [];
        $currentDate = $this->dateTime->getTodayDate();
        $rules = $this->ruleLoader->getRulesForApply($productId, $customerGroupId, $websiteId, $currentDate);
        foreach ($rules as $rule) {
            $appliedRuleIds[] = $rule->getId();
            if ($rule->getDiscardSubsequentRules()) {
                break;
            }
        }
        return $appliedRuleIds;
    }
}
