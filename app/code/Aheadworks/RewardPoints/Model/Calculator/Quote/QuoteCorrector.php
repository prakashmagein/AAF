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

namespace Aheadworks\RewardPoints\Model\Calculator\Quote;

use Aheadworks\RewardPoints\Model\Quote\QuoteResolver;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class QuoteCorrector
 */
class QuoteCorrector
{
    /**
     * QuoteCorrector constructor.
     *
     * @param OrderRepositoryInterface $orderRepository
     * @param QuoteResolver $quoteResolver
     */
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private QuoteResolver $quoteResolver
    ) {
    }

    /**
     * Get quote for invoice calculation
     *
     * @param int $orderId
     * @param CartInterface $calculationQuote
     * @return CartInterface
     * @throws NoSuchEntityException
     */
    public function getQuoteForInvoice(int $orderId, CartInterface $calculationQuote): CartInterface
    {
        try {
            $order = $this->orderRepository->get($orderId);
        } catch (NoSuchEntityException $e) {
            return $calculationQuote;
        }

        $areAllItemsInvoiced = true;
        foreach ($order->getItems() as $orderItem) {
            if ($orderItem->isDummy()) {
                continue;
            }
            if (((int)$orderItem->getQtyOrdered() - (int)$orderItem->getQtyInvoiced()) > 0) {
                $areAllItemsInvoiced = false;
                break;
            }
        }

        if ($areAllItemsInvoiced) {
            $calculationQuote = $this->quoteResolver->getQuote((int)$order->getQuoteId());
        }
        return $calculationQuote;
    }

    /**
     * Get quote for credit memo calculation
     *
     * @param int $orderId
     * @param CartInterface $calculationQuote
     * @return CartInterface
     * @throws NoSuchEntityException
     */
    public function getQuoteForCreditMemo(int $orderId, CartInterface $calculationQuote): CartInterface
    {
        try {
            $order = $this->orderRepository->get($orderId);
        } catch (NoSuchEntityException $e) {
            return $calculationQuote;
        }

        $hasOrderItemRefunded = false;
        foreach ($order->getItems() as $orderItem) {
            if ($orderItem->isDummy()) {
                continue;
            }
            if ((int)$orderItem->getQtyRefunded() > 0) {
                $hasOrderItemRefunded = true;
                break;
            }
        }

        if ($hasOrderItemRefunded) {
            $calculationQuote = $this->quoteResolver->getQuote((int)$order->getQuoteId());
        }
        return $calculationQuote;
    }
}
