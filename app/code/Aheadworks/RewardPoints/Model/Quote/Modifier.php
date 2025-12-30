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

namespace Aheadworks\RewardPoints\Model\Quote;

use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;

class Modifier
{
    /**
     * Modify quote by invoice or creditmemo
     *
     * @param CartInterface $quote
     * @param InvoiceInterface|CreditmemoInterface|OrderInterface $entity
     * @return CartInterface
     */
    public function modify(CartInterface $quote, $entity): CartInterface
    {
        $cloneQuote = clone $quote;

        if ((float)$quote->getSubtotal() !== (float)$entity->getSubtotal()) {
            $itemIds = [];
            $quoteItemsIds = [];

            foreach ($entity->getAllItems() as $item) {
                $itemIds[] = $item->getOrderItem()->getQuoteItemId();
                /** @var Quote $cloneQuote */
                $cloneQuote->getItemsCollection()->getItemById($item->getOrderItem()->getQuoteItemId())->setQty($item->getQty());
            }

            foreach ($cloneQuote->getItemsCollection()->getItems() as $key => $value) {
                $quoteItemsIds[] = $key;
            }
            $resultIds = array_diff($quoteItemsIds, $itemIds);

            foreach ($resultIds as $id) {
                if (!$cloneQuote->getItemsCollection()->getItemById($id)->getParentItemId()) {
                    $cloneQuote->getItemsCollection()->removeItemByKey($id);
                    continue;
                }
                $cloneQuote->getItemsCollection()->getItemById($id)->setQty(0);
            }
            $cloneQuote->collectTotals();

            return $cloneQuote;
        }

        return $quote;
    }
}
