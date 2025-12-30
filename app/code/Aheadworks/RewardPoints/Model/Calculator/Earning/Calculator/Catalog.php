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

namespace Aheadworks\RewardPoints\Model\Calculator\Earning\Calculator;

use Aheadworks\RewardPoints\Api\Data\EarnRuleInterface;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItem;
use Aheadworks\RewardPoints\Model\Calculator\ResultInterface;
use Aheadworks\RewardPoints\Model\Calculator\ResultInterfaceFactory;
use Aheadworks\RewardPoints\Model\DateTime;
use Aheadworks\RewardPoints\Model\EarnRule\Applier;
use Aheadworks\RewardPoints\Model\ResourceModel\EarnRule as EarnRuleResource;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Aheadworks\RewardPoints\Model\Calculator\AbstractCalculator;

/**
 * Class Catalog
 */
class Catalog extends AbstractCalculator implements CalculatorInterface
{
    /**
     * @var Applier
     */
    private $ruleApplier;

    /**
     * @var EarnRuleResource
     */
    private $earnRuleResource;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param Applier $ruleApplier
     * @param ResultInterfaceFactory $resultFactory
     * @param EarnRuleResource $earnRuleResource
     * @param DateTime $dateTime
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Applier $ruleApplier,
        ResultInterfaceFactory $resultFactory,
        EarnRuleResource $earnRuleResource,
        DateTime $dateTime,
        CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($resultFactory);
        $this->ruleApplier = $ruleApplier;
        $this->resultFactory = $resultFactory;
        $this->earnRuleResource = $earnRuleResource;
        $this->dateTime = $dateTime;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Calculate earning points for the customer
     *
     * @param EarnRuleInterface $rule
     * @param CalculationRequestInterface $calculationRequest
     * @return ResultInterface
     */
    public function calculate(EarnRuleInterface $rule, CalculationRequestInterface $calculationRequest): ResultInterface
    {
        $appliedRules = [];
        $points = $calculationRequest->getPoints();
        $currentDate = $this->dateTime->getTodayDate();
        $customer = $this->customerRepository->getById($calculationRequest->getCustomerId());
        $validProductIds = $this->earnRuleResource->getProductIdsByRyleToApply(
            $rule->getId(),
            (int)$customer->getGroupId(),
            $calculationRequest->getWebsiteId(),
            $currentDate
        );

        /** @var EarnItem $item */
        foreach ($calculationRequest->getItems() as $item) {
            if (in_array($item->getProductId(), $validProductIds)) {

                /** @var ResultInterface $applyResult */
                $applyResult = $this->ruleApplier->apply(
                    $item->getPoints(),
                    (float)$item->getQty(),
                    (int)$item->getProductId(),
                    (int)$calculationRequest->getCustomerId(),
                    (int)$calculationRequest->getWebsiteId(),
                    $rule
                );
                $points += $applyResult->getPoints() - $item->getPoints();
                $appliedRules = array_unique(array_merge($appliedRules, $applyResult->getAppliedRuleIds()));
            }
        }

        /** @var ResultInterface $result */
        $result = $this->resultFactory->create();
        $result
            ->setPoints((int)$points)
            ->setAppliedRuleIds($appliedRules);

        return $result;
    }

    /**
     * Calculate earning points for the customer group
     *
     * @param EarnRuleInterface $rule
     * @param CalculationRequestInterface $calculationRequest
     * @return ResultInterface
     */
    public function calculateByCustomerGroup(EarnRuleInterface $rule, CalculationRequestInterface $calculationRequest): ResultInterface
    {
        $appliedRules = [];
        $points = $calculationRequest->getPoints();
        $currentDate = $this->dateTime->getTodayDate();
        $validProductIds = $this->earnRuleResource->getProductIdsByRyleToApply(
            $rule->getId(),
            (int)$calculationRequest->getCustomerGroupId(),
            (int)$calculationRequest->getWebsiteId(),
            $currentDate
        );

        /** @var EarnItem $item */
        foreach ($calculationRequest->getItems() as $item) {
            if (in_array($item->getProductId(), $validProductIds)) {

                /** @var ResultInterface $applyResult */
                $applyResult = $this->ruleApplier->applyByCustomerGroup(
                    $item->getPoints(),
                    $item->getQty(),
                    (int)$item->getProductId(),
                    $calculationRequest->getCustomerGroupId(),
                    (int)$calculationRequest->getWebsiteId(),
                    $rule
                );
                $points += $applyResult->getPoints() - $item->getPoints();
                $appliedRules = array_unique(array_merge($appliedRules, $applyResult->getAppliedRuleIds()));
            }
        }

        /** @var ResultInterface $result */
        $result = $this->resultFactory->create();
        $result
            ->setPoints((int)$points)
            ->setAppliedRuleIds($appliedRules);

        return $result;
    }
}
