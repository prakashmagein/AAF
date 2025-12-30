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
namespace Aheadworks\RewardPoints\Model\Calculator;

/**
 * Interface ResultInterface
 * @package Aheadworks\RewardPoints\Model\Calculator
 */
interface ResultInterface
{
    /**#@+
     * Constants for keys.
     */
    const POINTS            = 'points';
    const APPLIED_RULE_IDS  = 'applied_rule_ids';
    /**#@-*/

    /**
     * Get points
     *
     * @return int
     */
    public function getPoints();

    /**
     * Set points
     *
     * @param int $points
     * @return $this
     */
    public function setPoints($points);

    /**
     * Get applied rule ids
     *
     * @return int[]
     */
    public function getAppliedRuleIds();

    /**
     * Set applied rule ids
     *
     * @param int[] $ruleIds
     * @return $this
     */
    public function setAppliedRuleIds($ruleIds);
}
