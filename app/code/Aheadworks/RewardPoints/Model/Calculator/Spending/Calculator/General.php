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

use Aheadworks\RewardPoints\Model\Calculator\ResultInterface;
use Aheadworks\RewardPoints\Model\Calculator\ResultInterfaceFactory;
use Psr\Log\LoggerInterface as Logger;
use Aheadworks\RewardPoints\Model\Calculator\AbstractCalculator;
use Aheadworks\RewardPoints\Api\SpendRuleManagementInterface;

/**
 * Class General
 */
class General extends AbstractCalculator
{
    /**
     * @param ResultInterfaceFactory $resultFactory
     * @param Pool $calculatorPool
     * @param Logger $logger
     * @param SpendRuleManagementInterface $spendRuleManagement
     */
    public function __construct(
        ResultInterfaceFactory $resultFactory,
        private Pool $calculatorPool,
        private Logger $logger,
        private SpendRuleManagementInterface $spendRuleManagement
    ) {
        parent::__construct($resultFactory);
    }

    /**
     * Retrieve calculation earning points value
     *
     * @param CalculationRequestInterface $calculationRequest
     * @return ResultInterface
     */
    public function calculateByRules(CalculationRequestInterface $calculationRequest): ResultInterface
    {
        $activeRules = $this->spendRuleManagement->getActiveRules([$calculationRequest->getWebsiteId()]);

        try {
            /** @var ResultInterface $result */
            $result = $this->resultFactory->create();
            $result->setAppliedRuleIds([]);
            if(!$activeRules){
                return $result;
            }
            foreach ($activeRules as $activeRule) {
                $calculator = $this->calculatorPool->getCalculator($activeRule->getType());
                $ruleResult = $calculator->calculate($activeRule, $calculationRequest);

                $result->setAppliedRuleIds(
                    array_unique(array_merge($ruleResult->getAppliedRuleIds(), $result->getAppliedRuleIds()))
                );

                if (in_array($activeRule->getId(), $result->getAppliedRuleIds()) &&
                    $activeRule->getDiscardSubsequentRules()) {
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
