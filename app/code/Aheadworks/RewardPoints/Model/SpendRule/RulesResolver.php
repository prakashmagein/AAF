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
use Aheadworks\RewardPoints\Api\SpendRuleManagementInterface;
use Aheadworks\RewardPoints\Model\Calculator\Spending\Calculator\CalculationRequestInterface;
use Aheadworks\RewardPoints\Model\ResourceModel\OrderExtra;

/**
 * Class RulesResolver
 */
class RulesResolver
{
    /**
     * @var SpendRuleManagementInterface
     */
    private $spendRuleManagement;

    /**
     * @var OrderExtra
     */
    private $orderExtra;

    /**
     * @param SpendRuleManagementInterface $spendRuleManagement
     */
    public function __construct(
        SpendRuleManagementInterface $spendRuleManagement,
        OrderExtra $orderExtra
    ) {
        $this->spendRuleManagement = $spendRuleManagement;
        $this->orderExtra = $orderExtra;
    }

    /**
     * Get earn rules
     *
     * @return SpendRuleInterface[]|null
     */
    public function getRules(CalculationRequestInterface $calculationRequest)
    {
        $rules = $this->spendRuleManagement->getActiveRules();
        if (!$calculationRequest->getOrderId()) {
            return $rules;
        }
        $appliedRuleIds = explode(',', $this->orderExtra->getAppliedRuleIds($calculationRequest->getOrderId()));
        if ($appliedRuleIds && !$calculationRequest->getIsCalculateForCreditMemo()) {
            $rules = $this->spendRuleManagement->getActiveRules([], $appliedRuleIds);
        }
        if ($appliedRuleIds && $calculationRequest->getIsCalculateForCreditMemo()) {
            $rules = $this->spendRuleManagement->getRulesByIds([], $appliedRuleIds);
        }

        return $rules;
    }
}
