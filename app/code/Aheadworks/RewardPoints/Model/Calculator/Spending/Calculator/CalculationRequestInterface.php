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

use Aheadworks\RewardPoints\Model\Calculator\Spending\SpendItemInterface;
use Magento\Quote\Model\Quote;

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
    public function setCustomerId(?int $customerId): CalculationRequestInterface;

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
    public function setCustomerGroupId(?int $customerGroupId): CalculationRequestInterface;

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
    public function setWebsiteId(?int $websiteId): CalculationRequestInterface;

    /**
     * Get items
     *
     * @return SpendItemInterface[]|null
     */
    public function getItems(): ?array;

    /**
     * Set items
     *
     * @param SpendItemInterface[] $items
     * @return $this
     */
    public function setItems(array $items): CalculationRequestInterface;

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
     * @return CalculationRequestInterface
     */
    public function setQuote(Quote $quote): CalculationRequestInterface;
}
