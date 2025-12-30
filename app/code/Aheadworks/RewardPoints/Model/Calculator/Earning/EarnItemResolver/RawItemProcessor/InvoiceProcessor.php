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
namespace Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\RawItemProcessor;

use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;

/**
 * Class InvoiceProcessor
 * @package Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\RawItemProcessor
 */
class InvoiceProcessor
{
    /**
     * @var InvoiceItemsResolver
     */
    private $invoiceItemsResolver;

    /**
     * @var ItemGroupConverterInterface
     */
    private $itemGroupConverter;

    /**
     * @param InvoiceItemsResolver $invoiceItemsResolver
     * @param ItemGroupConverterInterface $itemGroupConverter
     */
    public function __construct(
        InvoiceItemsResolver $invoiceItemsResolver,
        ItemGroupConverterInterface $itemGroupConverter
    ) {
        $this->invoiceItemsResolver = $invoiceItemsResolver;
        $this->itemGroupConverter = $itemGroupConverter;
    }

    /**
     * @param InvoiceInterface $invoice
     * @return array [ItemInterface[], ...]
     */
    public function getItemGroups($invoice)
    {
        /** @var InvoiceItem[] $invoiceItems */
        $invoiceItems = $this->invoiceItemsResolver->getItems($invoice);
        $invoiceItemGroups = $this->getInvoiceItemsGrouped($invoiceItems);
        $itemGroups = $this->itemGroupConverter->convert($invoiceItemGroups);

        return $itemGroups;
    }

    /**
     * Get invoice items grouped
     *
     * @param InvoiceItem[] $invoiceItems
     * @return array
     */
    private function getInvoiceItemsGrouped($invoiceItems)
    {
        $invoiceGroups = [];
        /** @var InvoiceItem $invoiceItem */
        foreach ($invoiceItems as $invoiceItem) {
            $parentItemId = $invoiceItem->getParentItemId();
            if ($parentItemId == null) {
                $parentItemId = $invoiceItem->getItemId();
            }
            $invoiceGroups[$parentItemId][$invoiceItem->getItemId()] = $invoiceItem;
        }
        return $invoiceGroups;
    }
}
