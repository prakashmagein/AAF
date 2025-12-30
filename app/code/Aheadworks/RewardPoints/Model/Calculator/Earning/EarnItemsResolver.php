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

namespace Aheadworks\RewardPoints\Model\Calculator\Earning;

use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ItemProcessor;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ProductProcessor;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\RawItemProcessor\CreditmemoProcessor;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\RawItemProcessor\InvoiceProcessor;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\RawItemProcessor\QuoteProcessor;
use Exception;
use Magento\Catalog\Model\Product;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\InvoiceInterface;

class EarnItemsResolver
{
    /**
     * @param QuoteProcessor $quoteProcessor
     * @param InvoiceProcessor $invoiceProcessor
     * @param CreditmemoProcessor $creditmemoProcessor
     * @param ProductProcessor $productProcessor
     * @param ItemProcessor $itemProcessor
     */
    public function __construct(
        private readonly QuoteProcessor $quoteProcessor,
        private readonly InvoiceProcessor $invoiceProcessor,
        private readonly CreditmemoProcessor $creditmemoProcessor,
        private readonly ProductProcessor $productProcessor,
        private readonly ItemProcessor $itemProcessor
    ) {
    }

    /**
     * Get earn items from quote
     *
     * @param CartInterface|Quote $quote
     * @param bool $beforeTax
     * @return EarnItemInterface[]
     * @throws Exception
     */
    public function getItemsByQuote(CartInterface|Quote $quote, bool $beforeTax = true): array
    {
        $itemGroups = $this->quoteProcessor->getItemGroups($quote);
        return $this->getEarnItems($itemGroups, $beforeTax);
    }

    /**
     * Get earn items from invoice
     *
     * @param InvoiceInterface $invoice
     * @param bool $beforeTax
     * @return EarnItemInterface[]
     * @throws Exception
     */
    public function getItemsByInvoice(InvoiceInterface $invoice, bool $beforeTax = true): array
    {
        $itemGroups = $this->invoiceProcessor->getItemGroups($invoice);
        return $this->getEarnItems($itemGroups, $beforeTax);
    }

    /**
     * Get earn items from creditmemo
     *
     * @param CreditmemoInterface $creditmemo
     * @param bool $beforeTax
     * @return EarnItemInterface[]
     * @throws Exception
     */
    public function getItemsByCreditmemo(CreditmemoInterface $creditmemo, bool $beforeTax = true): array
    {
        $itemGroups = $this->creditmemoProcessor->getItemGroups($creditmemo);
        return $this->getEarnItems($itemGroups, $beforeTax);
    }

    /**
     * Get earn items
     *
     * @param array $itemGroups
     * @param bool $beforeTax
     * @return EarnItemInterface[]
     * @throws Exception
     */
    private function getEarnItems(array $itemGroups, bool $beforeTax): array
    {
        $earnItems = [];
        foreach ($itemGroups as $itemGroup) {
            $earnItems[] = $this->itemProcessor->getEarnItem($itemGroup, $beforeTax);
        }
        return $earnItems;
    }

    /**
     * Get earn items by product
     *
     * @param Product $product
     * @param bool $beforeTax
     * @return EarnItemInterface[]
     * @throws Exception
     */
    public function getItemsByProduct(Product $product, bool $beforeTax = true): array
    {
        return $this->productProcessor->getEarnItems($product, $beforeTax);
    }
}
