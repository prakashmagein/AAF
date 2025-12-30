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

use Aheadworks\RewardPoints\Api\Data\EarnRateInterface;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItem;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemInterface;
use Aheadworks\RewardPoints\Model\Calculator\RateCalculator;
use Aheadworks\RewardPoints\Model\Calculator\ResultInterface;
use Aheadworks\RewardPoints\Model\Calculator\ResultInterfaceFactory;
use Aheadworks\RewardPoints\Model\EarnRule\RulesResolver;
use Aheadworks\RewardPoints\Model\Source\EarnRule\Type;
use Psr\Log\LoggerInterface as Logger;
use Aheadworks\RewardPoints\Model\Calculator\AbstractCalculator;

/**
 * Class General
 */
class General extends AbstractCalculator
{
    /**
     * @param RateCalculator $rateCalculator
     * @param RateResolver $rateResolver
     * @param ResultInterfaceFactory $resultFactory
     * @param Pool $calculatorPool
     * @param Logger $logger
     * @param RulesResolver $rulesResolver
     * @param Rounding $rounding
     */
    public function __construct(
        ResultInterfaceFactory $resultFactory,
        private readonly RateCalculator $rateCalculator,
        private readonly RateResolver $rateResolver,
        private readonly Pool $calculatorPool,
        private readonly Logger $logger,
        private readonly RulesResolver $rulesResolver,
        private readonly Rounding $rounding
    ) {
        parent::__construct($resultFactory);
    }

    /**
     * Calculate earning points for the customer
     *
     * @param EarnItemInterface[] $items
     * @param int $customerId
     * @param int|null $websiteId
     * @return ResultInterface
     */
    public function calculate(array $items, int $customerId, ?int $websiteId): ResultInterface
    {
        $points = 0;
        $appliedRules = [];
        /** @var EarnItem $item */
        foreach ($items as $item) {
            $itemPoints = $this->rateCalculator->calculateEarnPointsRaw(
                $customerId,
                $item->getBaseAmount(),
                $websiteId
            );

            $itemPoints = $this->rounding->apply($itemPoints, $websiteId);
            $item->setPoints($itemPoints);
            $points += $itemPoints;
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
     * @param EarnItemInterface[] $items
     * @param int $customerGroupId
     * @param int|null $websiteId
     * @return ResultInterface
     */
    public function calculateByCustomerGroup(array $items, int $customerGroupId, ?int $websiteId): ResultInterface
    {
        $points = 0;
        $appliedRules = [];
        /** @var EarnRateInterface|null $rate */
        $rate = $this->rateResolver->getEarnRate($customerGroupId, $websiteId);

        /** @var EarnItem $item */
        foreach ($items as $item) {
            $itemPoints = $rate
                ? $this->rateCalculator->calculateEarnPointsByRateRaw($rate, $item->getBaseAmount())
                : 0;

            $itemPoints = $this->rounding->apply($itemPoints, $websiteId);
            $item->setPoints($itemPoints);
            $points += $itemPoints;
        }

        /** @var ResultInterface $result */
        $result = $this->resultFactory->create();
        $result
            ->setPoints($points)
            ->setAppliedRuleIds($appliedRules);

        return $result;
    }

    /**
     * Retrieve calculation earning points value
     *
     * @param ResultInterface $result
     * @param CalculationRequestInterface $calculationRequest
     * @return ResultInterface
     */
    public function calculatePointsByRules(ResultInterface $result, CalculationRequestInterface $calculationRequest): ResultInterface
    {
        $activeRules = $this->rulesResolver->getRules($calculationRequest);

        try {
            if(!$activeRules){
                $result->setPoints((int)$result->getPoints());
                return $result;
            }

            foreach ($activeRules as $activeRule) {
                $calculator = $this->calculatorPool->getCalculator($activeRule->getType());

                if ($activeRule->getType() === Type::CART && !$calculationRequest->getIsNeedCalculateCartRule()) {
                    continue;
                }
                if (!$calculationRequest->getCustomerId()) {
                    $ruleResult = $calculator->calculateByCustomerGroup($activeRule, $calculationRequest);
                } else {
                    $ruleResult = $calculator->calculate($activeRule, $calculationRequest);
                }

                $result->setPoints($ruleResult->getPoints() == 0 ? (int)$result->getPoints() : (int)$ruleResult->getPoints());
                $calculationRequest->setPoints($result->getPoints());
                $result->setAppliedRuleIds(array_unique(array_merge($ruleResult->getAppliedRuleIds(), $result->getAppliedRuleIds())));

                if (in_array($activeRule->getId(), $result->getAppliedRuleIds()) && ($activeRule->getDiscardSubsequentRules())) {
                    break;
                }
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            $result = $calculator->getEmptyResult();
        }

        return $result;
    }
}
