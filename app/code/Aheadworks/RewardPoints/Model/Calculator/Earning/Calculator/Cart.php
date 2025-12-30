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
use Aheadworks\RewardPoints\Model\Calculator\ResultInterface;
use Aheadworks\RewardPoints\Model\Calculator\ResultInterfaceFactory;
use Aheadworks\RewardPoints\Model\EarnRule\Applier;
use Aheadworks\RewardPoints\Model\EarnRule\Condition\Rule\Loader as ConditionLoader;
use Aheadworks\RewardPoints\Model\Calculator\Quote\Address\Modifier as AddressModifier;
use Aheadworks\RewardPoints\Model\ResourceModel\OrderExtra;
use Magento\Quote\Model\Quote;
use Aheadworks\RewardPoints\Model\Calculator\AbstractCalculator;
use Aheadworks\RewardPoints\Model\Calculator\Quote\QuoteCorrector;

/**
 * Class Cart
 */
class Cart extends AbstractCalculator implements CalculatorInterface
{
    const ONE_QTY = 1;

    /**
     * @var Applier
     */
    private $ruleApplier;

    /**
     * @var ConditionLoader
     */
    private $conditionLoader;

    /**
     * @var AddressModifier
     */
    private $addressModifier;

    /**
     * @var OrderExtra
     */
    private $orderExtra;

    /**
     * @param Applier $ruleApplier
     * @param ResultInterfaceFactory $resultFactory
     * @param ConditionLoader $conditionLoader
     * @param AddressModifier $addressModifier
     * @param OrderExtra $orderExtra
     * @param QuoteCorrector $quoteCorrector
     */
    public function __construct(
        Applier $ruleApplier,
        ResultInterfaceFactory $resultFactory,
        ConditionLoader $conditionLoader,
        AddressModifier $addressModifier,
        OrderExtra $orderExtra,
        private QuoteCorrector $quoteCorrector
    ) {
        parent::__construct($resultFactory);
        $this->ruleApplier = $ruleApplier;
        $this->resultFactory = $resultFactory;
        $this->conditionLoader = $conditionLoader;
        $this->addressModifier = $addressModifier;
        $this->orderExtra = $orderExtra;
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
        $ruleConditions = $this->conditionLoader->loadCondition($rule);
        $appliedRules = [];

        /** @var Quote $quote */
        $quote = $calculationRequest->getQuote();
        if ($calculationRequest->getIsCalculateForInvoice()) {
            $quote = $this->quoteCorrector->getQuoteForInvoice($calculationRequest->getOrderId(), $quote);
        }
        if ($calculationRequest->getIsCalculateForCreditMemo()) {
            $quote = $this->quoteCorrector->getQuoteForCreditMemo($calculationRequest->getOrderId(), $quote);
        }

        $address = $quote->isVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
        $address = $this->addressModifier->modify($address, $quote);
        $orderId = $calculationRequest->getOrderId();

        $result = $this->resultFactory->create();
        $result
            ->setPoints(0)
            ->setAppliedRuleIds([]);

        if ($ruleConditions->validate($address) && !$calculationRequest->getIsCalculateForCreditMemo()) {
            /** @var ResultInterface $applyResult */
            $applyResult = $this->ruleApplier->apply(
                $calculationRequest->getPoints(),
                self::ONE_QTY,
                null,
                (int)$calculationRequest->getCustomerId(),
                (int)$calculationRequest->getWebsiteId(),
                $rule
            );
            $appliedRules = array_unique(array_merge($appliedRules, $applyResult->getAppliedRuleIds()));

            /** @var ResultInterface $result */
            $result
                ->setPoints((int)$applyResult->getPoints())
                ->setAppliedRuleIds($appliedRules);
            return $result;
        }

        $canceledRuleIds = explode(',', $this->orderExtra->getCanceledRuleIds($orderId));
        if (in_array($rule->getId(), $canceledRuleIds)) {
            return $result;
        }

        if (!in_array($rule->getId(), $canceledRuleIds) && $ruleConditions->validate($address)) {
            /** @var ResultInterface $applyResult */
            $applyResult = $this->ruleApplier->apply(
                $calculationRequest->getPoints(),
                self::ONE_QTY,
                null,
                (int)$calculationRequest->getCustomerId(),
                (int)$calculationRequest->getWebsiteId(),
                $rule
            );
            $appliedRules = array_unique(array_merge($appliedRules, $applyResult->getAppliedRuleIds()));

            /** @var ResultInterface $result */
            $result
                ->setPoints((int)$applyResult->getPoints())
                ->setAppliedRuleIds($appliedRules);

            return $result;
        }

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
        $ruleConditions = $this->conditionLoader->loadCondition($rule);
        $appliedRules = [];
        /** @var Quote $quote */
        $quote = $calculationRequest->getQuote();
        $address = $quote->isVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
        $address = $this->addressModifier->modify($address, $quote);

        if ($ruleConditions->validate($address)) {
            /** @var ResultInterface $applyResult */
            $applyResult = $this->ruleApplier->applyByCustomerGroup(
                $calculationRequest->getPoints(),
                self::ONE_QTY,
                null,
                $calculationRequest->getCustomerGroupId(),
                $calculationRequest->getWebsiteId(),
                $rule
            );

            $appliedRules = array_unique(array_merge($appliedRules, $applyResult->getAppliedRuleIds()));

            /** @var ResultInterface $result */
            $result = $this->resultFactory->create();
            $result
                ->setPoints((int)$applyResult->getPoints())
                ->setAppliedRuleIds($appliedRules);
            return $result;
        }

        $result = $this->resultFactory->create();
        $result
            ->setPoints(0)
            ->setAppliedRuleIds([]);

        return $result;
    }
}
