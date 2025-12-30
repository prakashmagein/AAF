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
use Magento\Framework\Api\AbstractSimpleObject;
use Magento\Quote\Model\Quote;

/**
 * Class CalculationRequest
 */
class CalculationRequest extends AbstractSimpleObject implements CalculationRequestInterface
{
    /**#@+
     * Constants for keys.
     */
    const CUSTOMER_ID = 'customer_id';
    const CUSTOMER_GROUP_ID = 'customer_group_id';
    const WEBSITE_ID = 'website_id';
    const ITEMS = 'items';
    const QUOTE = 'quote';
    /**#@-*/

    /**
     * Get customer id
     *
     * @return int|null
     */
    public function getCustomerId(): ?int
    {
        return $this->_get(self::CUSTOMER_ID);
    }

    /**
     * Set customer id
     *
     * @param int|null $customerId
     * @return $this
     */
    public function setCustomerId(?int $customerId): CalculationRequestInterface
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Get customer group id
     *
     * @return int|null
     */
    public function getCustomerGroupId(): ?int
    {
        return $this->_get(self::CUSTOMER_GROUP_ID);
    }

    /**
     * Set customer group id
     *
     * @param int|null $customerGroupId
     * @return $this
     */
    public function setCustomerGroupId(?int $customerGroupId): CalculationRequestInterface
    {
        return $this->setData(self::CUSTOMER_GROUP_ID, $customerGroupId);
    }

    /**
     * Get website id
     *
     * @return int|null
     */
    public function getWebsiteId(): ?int
    {
        return $this->_get(self::WEBSITE_ID);
    }

    /**
     * Set website id
     *
     * @param int|null $websiteId
     * @return $this
     */
    public function setWebsiteId(?int $websiteId): CalculationRequestInterface
    {
        return $this->setData(self::WEBSITE_ID, $websiteId);
    }

    /**
     * Get items
     *
     * @return SpendItemInterface[]|null
     */
    public function getItems(): ?array
    {
        return $this->_get(self::ITEMS);
    }

    /**
     * Set items
     *
     * @param SpendItemInterface[] $items
     * @return $this
     */
    public function setItems(array $items): CalculationRequestInterface
    {
        return $this->setData(self::ITEMS, $items);
    }

    /**
     * Get quote
     *
     * @return Quote|null
     */
    public function getQuote(): ?Quote
    {
        return $this->_get(self::QUOTE);
    }

    /**
     * Set quote
     *
     * @param Quote $quote
     * @return CalculationRequestInterface
     */
    public function setQuote(Quote $quote): CalculationRequestInterface
    {
        return $this->setData(self::QUOTE, $quote);
    }
}
