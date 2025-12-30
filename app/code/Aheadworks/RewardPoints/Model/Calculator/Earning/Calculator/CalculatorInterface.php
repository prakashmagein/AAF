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

/**
 * Interface CalculatorInterface
 */
interface CalculatorInterface
{
    /**
     * Calculate earning points for the customer
     *
     * @return ResultInterface
     */
    public function calculate(EarnRuleInterface $rule, CalculationRequestInterface $calculationRequest): ResultInterface;

    /**
     * Calculate earning points for the customer group
     *
     * @return ResultInterface
     */
    public function calculateByCustomerGroup(EarnRuleInterface $rule, CalculationRequestInterface $calculationRequest): ResultInterface;
}
