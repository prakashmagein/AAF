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
use Aheadworks\RewardPoints\Api\EarnRuleManagementInterface;
use Aheadworks\RewardPoints\Model\Calculator\Earning\Calculator\CalculationRequestInterface;
use Aheadworks\RewardPoints\Model\ResourceModel\OrderExtra;

/**
 * Class RulesResolver
 */
class RulesResolver
{
    /**
     * @var EarnRuleManagementInterface
     */
    private $earnRuleManagement;

    /**
     * @var OrderExtra
     */
    private $orderExtra;

    /**
     * @param EarnRuleManagementInterface $earnRuleManagement
     */
    public function __construct(
        EarnRuleManagementInterface $earnRuleManagement,
        OrderExtra $orderExtra
    ) {
        $this->earnRuleManagement = $earnRuleManagement;
        $this->orderExtra = $orderExtra;
    }

    /**
     * Get earn rules
     *
     * @return EarnRuleInterface[]|null
     */
    public function getRules(CalculationRequestInterface $calculationRequest)
    {
        $rules = $this->earnRuleManagement->getActiveRules([]);
        if (!$calculationRequest->getOrderId()) {
            return $rules;
        }
        $appliedRuleIds = explode(',', $this->orderExtra->getAppliedRuleIds($calculationRequest->getOrderId()));
        if ($appliedRuleIds && !$calculationRequest->getIsCalculateForCreditMemo()) {
            $rules = $this->earnRuleManagement->getActiveRules($appliedRuleIds);
        }
        if ($appliedRuleIds && $calculationRequest->getIsCalculateForCreditMemo()) {
            $rules = $this->earnRuleManagement->getRulesByIds($appliedRuleIds);
        }

        return $rules;
    }
}
