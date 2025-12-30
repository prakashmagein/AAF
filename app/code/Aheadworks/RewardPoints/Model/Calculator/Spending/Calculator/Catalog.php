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

namespace Aheadworks\RewardPoints\Model\Calculator\Spending\Calculator;

use Aheadworks\RewardPoints\Api\Data\SpendRuleInterface;
use Aheadworks\RewardPoints\Model\Calculator\Spending\SpendItem;
use Aheadworks\RewardPoints\Model\Calculator\ResultInterface;
use Aheadworks\RewardPoints\Model\Calculator\ResultInterfaceFactory;
use Aheadworks\RewardPoints\Model\DateTime;
use Aheadworks\RewardPoints\Model\SpendRule\Applier;
use Aheadworks\RewardPoints\Model\ResourceModel\SpendRule as SpendRuleResource;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Aheadworks\RewardPoints\Model\Calculator\AbstractCalculator;

/**
 * Class Catalog
 */
class Catalog extends AbstractCalculator implements CalculatorInterface
{
    /**
     * @param Applier $ruleApplier
     * @param ResultInterfaceFactory $resultFactory
     * @param SpendRuleResource $spendRuleResource
     * @param DateTime $dateTime
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        ResultInterfaceFactory $resultFactory,
        private Applier $ruleApplier,
        private SpendRuleResource $spendRuleResource,
        private DateTime $dateTime,
        private CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($resultFactory);
    }

    /**
     * Calculate spending points for the customer
     *
     * @param SpendRuleInterface $rule
     * @param CalculationRequestInterface $calculationRequest
     * @return ResultInterface
     */
    public function calculate(
        SpendRuleInterface $rule,
        CalculationRequestInterface $calculationRequest
    ): ResultInterface {
        $appliedRules = [];
        $currentDate = $this->dateTime->getTodayDate();

        $customerGroupId = $calculationRequest->getCustomerGroupId();
        $customerId = $calculationRequest->getCustomerId();
        if (!$customerGroupId) {
            $customer = $this->customerRepository->getById($customerId);
            $customerGroupId = (int)$customer->getGroupId();
        }

        $validProductIds = $this->spendRuleResource->getProductIdsByRyleToApply(
            $rule->getId(),
            $customerGroupId,
            $calculationRequest->getWebsiteId(),
            $currentDate
        );

        /** @var SpendItem $item */
        foreach ($calculationRequest->getItems() as $item) {
            if (in_array($item->getProductId(), $validProductIds)) {
                $this->ruleApplier->apply(
                    $customerId,
                    $customerGroupId,
                    (int)$calculationRequest->getWebsiteId(),
                    $rule,
                    $item
                );

                $appliedRules = array_unique(array_merge($appliedRules, $item->getAppliedRuleIds()));
            }
        }

        /** @var ResultInterface $result */
        $result = $this->resultFactory->create();
        $result->setAppliedRuleIds($appliedRules);

        return $result;
    }
}
