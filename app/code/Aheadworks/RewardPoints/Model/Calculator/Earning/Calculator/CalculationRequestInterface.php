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

use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Interface СalculationRequestInterface
 */
interface CalculationRequestInterface
{
    /**
     * Get customer id
     *
     * @return int|null
     */
    public function getCustomerId(): ?int;

    /**
     * Set customer id
     *
     * @param int|null $customerId
     * @return $this
     */
    public function setCustomerId(?int $customerId);

    /**
     * Get customer group id
     *
     * @return int|null
     */
    public function getCustomerGroupId(): ?int;

    /**
     * Set customer group id
     *
     * @param int|null $customerGroupId
     * @return $this
     */
    public function setCustomerGroupId(?int $customerGroupId);

    /**
     * Get website id
     *
     * @return int|null
     */
    public function getWebsiteId(): ?int;

    /**
     * Set website id
     *
     * @param int|null $websiteId
     * @return $this
     */
    public function setWebsiteId(?int $websiteId);

    /**
     * Get quote
     *
     * @return Quote|null
     */
    public function getQuote(): ?Quote;

    /**
     * Set quote
     *
     * @param Quote $quote
     * @return $this
     */
    public function setQuote(Quote $quote);

    /**
     * Get items
     *
     * @return EarnItemInterface[]|null
     */
    public function getItems(): ?array;

    /**
     * Set items
     *
     * @param EarnItemInterface[] $items
     * @return $this
     */
    public function setItems(array $items);

    /**
     * Get points
     *
     * @return float|null
     */
    public function getPoints(): ?float;

    /**
     * Set website id
     *
     * @param float $points
     * @return $this
     */
    public function setPoints(float $points);

    /**
     * Get need calculate cart rule
     *
     * @return bool
     */
    public function getIsNeedCalculateCartRule(): bool;

    /**
     * Set need calculate cart rule
     *
     * @param bool $needCalculateCartRule
     * @return $this
     */
    public function setIsNeedCalculateCartRule(bool $needCalculateCartRule);

    /**
     * Get order id
     *
     * @return int|null
     */
    public function getOrderId(): ?int;

    /**
     * Set order id
     *
     * @param int|null $orderId
     * @return $this
     */
    public function setOrderId(?int $orderId);

    /**
     * Get is calculate for credit memo value
     *
     * @return bool
     */
    public function getIsCalculateForCreditMemo(): bool;

    /**
     * Set is calculate for credit memo value
     *
     * @param bool $calculateForCreditmemo
     * @return $this
     */
    public function setIsCalculateForCreditMemo(bool $calculateForCreditMemo);

    /**
     * Get is calculate for invoice value
     *
     * @return bool
     */
    public function getIsCalculateForInvoice(): bool;

    /**
     * Set is calculate for invoice value
     *
     * @param bool $isCalculateForInvoice
     * @return $this
     */
    public function setIsCalculateForInvoice(bool $isCalculateForInvoice): CalculationRequestInterface;
}
