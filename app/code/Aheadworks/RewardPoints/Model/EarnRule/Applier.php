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

namespace Aheadworks\RewardPoints\Model\EarnRule;

use Aheadworks\RewardPoints\Api\Data\EarnRuleInterface;
use Aheadworks\RewardPoints\Model\DateTime;
use Aheadworks\RewardPoints\Model\EarnRule\Applier\ActionApplier;
use Aheadworks\RewardPoints\Model\EarnRule\Applier\RuleLoader;
use Aheadworks\RewardPoints\Model\Calculator\ResultInterface;
use Aheadworks\RewardPoints\Model\Calculator\ResultInterfaceFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Applier
 */
class Applier
{
    /**
     * @var RuleLoader
     */
    private $ruleLoader;

    /**
     * @var ActionApplier
     */
    private $actionApplier;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var ResultInterfaceFactory
     */
    private $resultFactory;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @param RuleLoader $ruleLoader
     * @param ActionApplier $actionApplier
     * @param CustomerRepositoryInterface $customerRepository
     * @param ResultInterfaceFactory $resultFactory
     * @param DateTime $dateTime
     */
    public function __construct(
        RuleLoader $ruleLoader,
        ActionApplier $actionApplier,
        CustomerRepositoryInterface $customerRepository,
        ResultInterfaceFactory $resultFactory,
        DateTime $dateTime
    ) {
        $this->ruleLoader = $ruleLoader;
        $this->actionApplier = $actionApplier;
        $this->customerRepository = $customerRepository;
        $this->resultFactory = $resultFactory;
        $this->dateTime = $dateTime;
    }

    /**
     * Apply earning rules
     *
     * @param float $points
     * @param float $qty
     * @param int|null $productId
     * @param int $customerId
     * @param int|null $websiteId
     * @param EarnRuleInterface $rule
     * @return ResultInterface
     */
    public function apply(
        float             $points,
        float             $qty,
        ?int              $productId,
        int               $customerId,
        ?int              $websiteId,
        EarnRuleInterface $rule
    ): ResultInterface {

        $appliedRuleIds = [];
        try {
            /** @var CustomerInterface $customer */
            $customer = $this->customerRepository->getById($customerId);
            $result = $this->applyByCustomerGroup($points, $qty, $productId, (int)$customer->getGroupId(), $websiteId , $rule);
        } catch (LocalizedException $e) {
            /** @var ResultInterface $result */
            $result = $this->resultFactory->create();
            $result
                ->setPoints((int)$points)
                ->setAppliedRuleIds($appliedRuleIds);
        }

        return $result;
    }

    /**
     * Apply earning rules by customer group
     *
     * @param float $points
     * @param float $qty
     * @param int|null $productId
     * @param int $customerGroupId
     * @param int|null $websiteId
     * @param EarnRuleInterface $rule
     * @return ResultInterface
     */
    public function applyByCustomerGroup(
        float             $points,
        float             $qty,
        ?int              $productId,
        int               $customerGroupId,
        ?int              $websiteId,
        EarnRuleInterface $rule
    ): ResultInterface {

        $appliedRuleIds = [];

        if (in_array($customerGroupId, $rule->getCustomerGroupIds()) && in_array($websiteId, $rule->getWebsiteIds())) {
            $points = $this->actionApplier->apply($points, $qty, $rule->getAction());
            $appliedRuleIds[] = $rule->getId();
        }

        /** @var ResultInterface $result */
        $result = $this->resultFactory->create();
        $result
            ->setPoints((int)$points)
            ->setAppliedRuleIds($appliedRuleIds);

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
    public function getAppliedRuleIds($productId, $customerGroupId, $websiteId)
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
