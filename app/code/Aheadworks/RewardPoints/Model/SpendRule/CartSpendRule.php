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

use Magento\Checkout\Model\Session as CheckoutSession;

/**
 * Class CartSpendRule
 */
class CartSpendRule
{
    /**
     * CartSpendRule constructor.
     *
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(private CheckoutSession $checkoutSession)
    {
    }

    /**
     * Set rule ids to checkout session
     *
     * @param int[] $ruleIds
     * @return void
     */
    public function setCurrentRuleIds(array $ruleIds): void
    {
        $this->checkoutSession->setCurrentCartSpendRuleIds($ruleIds);
    }

    /**
     * Get rule ids from checkout session
     *
     * @return int[]|null
     */
    public function getCurrentRuleIds(): ?array
    {
        return $this->checkoutSession->getCurrentCartSpendRuleIds();
    }

    /**
     * Remove current cart spend rule ids from checkout session
     *
     * @return void
     */
    public function removeCurrentRuleIds(): void
    {
        $this->checkoutSession->unsCurrentCartSpendRuleIds();
    }
}
