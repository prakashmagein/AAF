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
use Aheadworks\RewardPoints\Model\Calculator\ResultInterface;
use Aheadworks\RewardPoints\Model\Calculator\ResultInterfaceFactory;
use Aheadworks\RewardPoints\Model\SpendRule\Applier;
use Aheadworks\RewardPoints\Model\SpendRule\Condition\Rule\Loader as ConditionLoader;
use Aheadworks\RewardPoints\Model\Calculator\Quote\Address\Modifier as AddressModifier;
use Magento\Quote\Model\Quote;
use Aheadworks\RewardPoints\Model\Calculator\AbstractCalculator;

/**
 * Class Cart
 */
class Cart extends AbstractCalculator implements CalculatorInterface
{
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
     * @param Applier $ruleApplier
     * @param ResultInterfaceFactory $resultFactory
     * @param ConditionLoader $conditionLoader
     * @param AddressModifier $addressModifier
     */
    public function __construct(
        Applier $ruleApplier,
        ResultInterfaceFactory $resultFactory,
        ConditionLoader $conditionLoader,
        AddressModifier $addressModifier
    ) {
        parent::__construct($resultFactory);
        $this->ruleApplier = $ruleApplier;
        $this->resultFactory = $resultFactory;
        $this->conditionLoader = $conditionLoader;
        $this->addressModifier = $addressModifier;
    }

    /**
     * Calculate earning points for the customer
     *
     * @param SpendRuleInterface $rule
     * @param CalculationRequestInterface $calculationRequest
     * @return ResultInterface
     */
    public function calculate(
        SpendRuleInterface $rule,
        CalculationRequestInterface $calculationRequest
    ): ResultInterface {
        $ruleConditions = $this->conditionLoader->loadCondition($rule);
        $appliedRules = [];

        /** @var Quote $quote */
        $quote = $calculationRequest->getQuote();

        $address = $quote->isVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
        $address = $this->addressModifier->modify($address, $quote);

        $result = $this->resultFactory->create();
        $result->setAppliedRuleIds([]);

        if ($ruleConditions->validate($address)) {
            foreach ($calculationRequest->getItems() as $item) {
                /** @var ResultInterface $applyItemResult */
                $this->ruleApplier->apply(
                    $calculationRequest->getCustomerId(),
                    $calculationRequest->getCustomerGroupId(),
                    (int)$calculationRequest->getWebsiteId(),
                    $rule,
                    $item
                );
                $appliedRules += (array)$item->getAppliedRuleIds();
            }

            $appliedRules = array_unique($appliedRules);

            /** @var ResultInterface $result */
            $result->setAppliedRuleIds($appliedRules);
        }

        return $result;
    }
}
