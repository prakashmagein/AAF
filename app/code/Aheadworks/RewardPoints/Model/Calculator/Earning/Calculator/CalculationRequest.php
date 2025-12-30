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

use Magento\Framework\Api\AbstractSimpleObject;
use Magento\Quote\Model\Quote;
use Magento\Sales\Api\Data\OrderInterface;

class CalculationRequest extends AbstractSimpleObject implements CalculationRequestInterface
{
    /**#@+
     * Constants for keys.
     */
    const CUSTOMER_ID = 'customer_id';
    const CUSTOMER_GROUP_ID = 'customer_group_id';
    const WEBSITE_ID = 'website_id';
    const QUOTE = 'qoute';
    const ITEMS = 'items';
    const POINTS = 'points';
    const IS_NEED_CALCULATE_CART_RULE = 'is_need_calculate_cart_rule';
    const ORDER_ID = 'order_id';
    const CALCULATE_FOR_CREDITMEMO = 'calculate_for_creditmemo';
    const CALCULATE_FOR_INVOICE = 'calculate_for_invoice';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function getCustomerId(): ?int
    {
        return $this->_get(self::CUSTOMER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerGroupId(): ?int
    {
        return $this->_get(self::CUSTOMER_GROUP_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerGroupId($customerGroupId)
    {
        return $this->setData(self::CUSTOMER_GROUP_ID, $customerGroupId);
    }

    /**
     * {@inheritdoc}
     */
    public function getWebsiteId(): ?int
    {
        return $this->_get(self::WEBSITE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setWebsiteId($websiteId)
    {
        return $this->setData(self::WEBSITE_ID, $websiteId);
    }

    /**
     * {@inheritdoc}
     */
    public function getQuote(): ?Quote
    {
        return $this->_get(self::QUOTE);
    }

    /**
     * {@inheritdoc}
     */
    public function setQuote($quote)
    {
        return $this->setData(self::QUOTE, $quote);
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(): ?array
    {
        return $this->_get(self::ITEMS);
    }

    /**
     * {@inheritdoc}
     */
    public function setItems($items)
    {
        return $this->setData(self::ITEMS, $items);
    }

    /**
     * {@inheritdoc}
     */
    public function getPoints(): ?float
    {
        return $this->_get(self::POINTS);
    }

    /**
     * {@inheritdoc}
     */
    public function setPoints($points)
    {
        return $this->setData(self::POINTS, $points);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsNeedCalculateCartRule(): bool
    {
        return $this->_get(self::IS_NEED_CALCULATE_CART_RULE);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsNeedCalculateCartRule(bool $isNeedCalculateCartRule)
    {
        return $this->setData(self::IS_NEED_CALCULATE_CART_RULE, $isNeedCalculateCartRule);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderId(): ?int
    {
        return $this->_get(self::ORDER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderId(?int $orderId): self
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsCalculateForCreditMemo(): bool
    {
        return $this->_get(self::CALCULATE_FOR_CREDITMEMO);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsCalculateForCreditMemo(bool $calculateForCreditmemo): self
    {
        return $this->setData(self::CALCULATE_FOR_CREDITMEMO, $calculateForCreditmemo);
    }

    /**
     * Get is calculate for invoice value
     *
     * @return bool
     */
    public function getIsCalculateForInvoice(): bool
    {
        return $this->_get(self::CALCULATE_FOR_INVOICE);
    }

    /**
     * Set is calculate for invoice value
     *
     * @param bool $isCalculateForInvoice
     * @return $this
     */
    public function setIsCalculateForInvoice(bool $isCalculateForInvoice): CalculationRequestInterface
    {
        return $this->setData(self::CALCULATE_FOR_INVOICE, $isCalculateForInvoice);
    }
}
