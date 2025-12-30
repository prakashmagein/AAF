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

namespace Aheadworks\RewardPoints\Model;

interface OrderExtraInterface
{
    /**#@+
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case
     */
    const ORDER_ID = 'order_id';
    const APPLIED_RULE_IDS = 'applied_rule_ids';
    const CANCELED_RULE_IDS = 'canceled_rule_ids';
    /**#@-*/

    /**
     * Set order id
     *
     * @param  int $orderId
     * @return OrderExtraInterface
     */
    public function setOrderId(int $orderId): self;

    /**
     * Get order id
     *
     * @return int
     */
    public function getOrderId(): int;

    /**
     * Set applied rule ids
     *
     * @param  array $ruleIds
     * @return OrderExtraInterface
     */
    public function setAppliedRuleIds(array $ruleIds): self;

    /**
     * Get applied rule ids
     *
     * @return array|null
     */
    public function getAppliedRuleIds(): ?array ;

    /**
     * Set canceled rule ids
     *
     * @param  array $ruleIds
     * @return OrderExtraInterface
     */
    public function setCanceledRuleIds(array $ruleIds): self;

    /**
     * Get canceled rule ids
     *
     * @return array|null
     */
    public function getCanceledRuleIds(): ?array ;
}
