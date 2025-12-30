<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Model\Order;

use Magento\Sales\Model\Order;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\CatalogInventory\Observer\ProductQty;
use Magento\CatalogInventory\Model\StockManagement;
use Magento\Sales\Model\Order\ItemRepository as OrderItemRepository;
use Magento\Sales\Model\Order\Item as OrderItem;
use Magento\Quote\Model\Quote\Item\ToOrderItem;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Sales\Model\ResourceModel\Order\Tax\ItemFactory;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;
use Magento\Sales\Model\Order\Invoice\ItemRepository as InvoiceItemRepository;
use Magefan\OrderEdit\Model\Quote\TaxManager;
use Magefan\OrderEdit\Model\Config;

class UpdateOrderItems extends AbstractUpdateOrder
{
    const SKIP_PARENT_ITEM_ID = 100000000;

    /**
     * @var QuoteItem
     */
    protected $quoteItem;

    /**
     * @var ProductQty
     */
    protected $productQty;

    /**
     * @var StockManagement
     */
    protected $stockManager;

    /**
     * @var OrderItemRepository
     */
    protected $orderItemRepository;

    /**
     * @var ToOrderItem
     */
    protected $toOrderItem;

    /**
     * @var ItemFactory
     */
    protected $orderItemTaxFactory;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var InvoiceItemRepository
     */
    protected $invoiceItemRepository;

    /**
     * @var Config
     */
    protected $config;

    protected $orderItemAppliedTaxes;

    protected $order;

    protected $quote;

    protected $connection;

    protected $missingOrderTaxItems;

    protected $orderTaxId;
    protected $invoicesOrderItemsIds = null;

    protected $quoteItemsMap = null;

    protected $composites = null;

    protected $newOrderTotals = [
        'subTotal' => 0.0,
        'subTotalInclTax' => 0.0,
        'baseSubTotal' => 0.0,
        'baseSubTotalInclTax' => 0.0,
        'discountTotal' => 0.0,
        'baseDiscountTotal' => 0.0,
        'taxTotal' => 0.0,
        'baseTaxTotal' => 0.0,
    ];

    protected $processedQuoteItemsIds = [];

    /**
     * @param StoreManagerInterface $storeManager
     * @param TaxManager $taxManager
     * @param QuoteItem $quoteItem
     * @param ProductQty $productQty
     * @param StockManagement $stockManager
     * @param OrderItemRepository $orderItemRepository
     * @param ToOrderItem $toOrderItem
     * @param Config $config
     * @param ItemFactory|null $orderItemTaxFactory
     * @param ResourceConnection|null $resourceConnection
     * @param LoggerInterface|null $logger
     * @param InvoiceItemRepository|null $invoiceItemRepository
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        TaxManager $taxManager,
        QuoteItem $quoteItem,
        ProductQty $productQty,
        StockManagement $stockManager,
        OrderItemRepository $orderItemRepository,
        ToOrderItem $toOrderItem,
        Config $config,
        ItemFactory $orderItemTaxFactory = null,
        ResourceConnection $resourceConnection = null,
        LoggerInterface $logger = null,
        InvoiceItemRepository $invoiceItemRepository = null
    ) {
        $this->quoteItem = $quoteItem;
        $this->productQty = $productQty;
        $this->stockManager = $stockManager;
        $this->toOrderItem = $toOrderItem;
        $this->config = $config;
        $this->orderItemRepository = $orderItemRepository;
        $this->orderItemTaxFactory = $orderItemTaxFactory ?:  \Magento\Framework\App\ObjectManager::getInstance()
            ->get(ItemFactory::class);
        $this->resourceConnection = $resourceConnection ?:  \Magento\Framework\App\ObjectManager::getInstance()
            ->get(ResourceConnection::class);
        $this->logger = $logger ?:  \Magento\Framework\App\ObjectManager::getInstance()
            ->get(LoggerInterface::class);
        $this->invoiceItemRepository = $invoiceItemRepository ?:  \Magento\Framework\App\ObjectManager::getInstance()
            ->get(InvoiceItemRepository::class);
        parent::__construct($storeManager, $taxManager);
    }

    /**
     * @param Order $order
     * @param array $logOfChanges
     * @param Quote|null $quote
     * @return bool
     * @throws \Magento\CatalogInventory\Model\StockStateException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Order $order, array &$logOfChanges, Quote $quote = null): bool
    {
        $this->prepareItems($quote);
        $this->order = $order;
        $this->quote = $quote;

        $composites = $this->getCompositeItems($order);
        foreach ($order->getAllItems() as $orderItem) {
            if (isset($composites[$orderItem->getId()])) {
                continue;
            }

            $childOrderItem = false;

            $product = $this->getProductAndConfigureOrderItemChildItemRelation($orderItem, $childOrderItem);
            $quoteItemId = $this->getQuoteItemIdForProduct($product);

            if (!$quoteItemId) {
                $this->removeOrderItem($orderItem, $product->getId(), $childOrderItem);
            } elseif (isset($this->quoteItemsMap[$quoteItemId])) {
                $this->editOrderItemWithQuoteItem($orderItem, $quoteItemId, $logOfChanges, $childOrderItem);
            }
        }

        //Add remaining items
        $this->addNewItemsToOrder();

        $this->updateOrderTotals($logOfChanges);

        //Order Tax Manager (maybe move to class,since it can be used not only for items updating but also for shipping method updating)
        foreach ($order->getAllItems() as $orderItem) {
            //Every func call -> db query
            $this->updateTaxItem($orderItem);
        }
        //One db query
        $this->insertMissingOrderTaxItems();
        //One db query
        $this->deleteOrderTaxItemsWithNoItemId();
        //One db query
        $this->deleteOrderTaxItemsWithTypeFee();
        //One db query
        $this->taxManager->updateOrderTax($order, $quote);


        return true;
    }

    /**
     * @param $processedQuoteItemsIds
     * @param $product
     * @return int
     */
    protected function getQuoteItemIdForProduct($product): int
    {
        foreach ($this->quote->getAllItems() as $quoteItem) {
            if (!in_array((int)$quoteItem->getId(), $this->processedQuoteItemsIds) && $quoteItem->representProduct($product)) {
                $this->processedQuoteItemsIds[] = (int)$quoteItem->getId();
                return (int)$quoteItem->getId();
            }
        }

        return 0;
    }

    /**
     * @param $orderItem
     * @param $childOrderItem
     * @return mixed
     */
    protected function getProductAndConfigureOrderItemChildItemRelation(&$orderItem, &$childOrderItem)
    {
        $composites = $this->getCompositeItems($this->order);

        $product = $orderItem->getProduct();

        if (isset($composites[$orderItem->getParentItemId()])) {
            $parentItem = $this->order->getItemById($orderItem->getParentItemId());

            //We must work with child item data(price,qty ...) when bundle
            if ('bundle' === $composites[$orderItem->getParentItemId()]->getProductType()) {
                $quoteItem = $this->quote->getItemById($orderItem->getQuoteItemId());
                $childOrderItem = $parentItem;
            } else {
                //We must work with parent item data(price,qty ...) when configurable
                $quoteItem = $this->quote->getItemById($parentItem->getQuoteItemId());
                $childOrderItem = $orderItem;
                $orderItem = $parentItem;
            }
        } else {
            $quoteItem = $this->quote->getItemById($orderItem->getQuoteItemId());
        }

        if ($quoteItem && $quoteItem->getId()) {
            $product = $quoteItem->getProduct();
        }

        return $product;
    }

    /**
     * @param $logOfChanges
     * @return void
     */
    protected function updateOrderTotals(&$logOfChanges): void
    {
        $this->simpleOrderFieldChange('getSubtotal', $logOfChanges, $this->newOrderTotals['subTotal']);
        $this->simpleOrderFieldChange('getBaseSubtotal', $logOfChanges, $this->newOrderTotals['baseSubTotal']);
        $this->simpleOrderFieldChange('getSubtotalInclTax', $logOfChanges, $this->newOrderTotals['subTotalInclTax']);
        $this->simpleOrderFieldChange('getBaseSubtotalInclTax', $logOfChanges, $this->newOrderTotals['baseSubTotalInclTax']);

        $orderCurrentDiscountAmount = (float)$this->order->getDiscountAmount();
        $orderCurrentBaseDiscountAmount = (float)$this->order->getBaseDiscountAmount();
        $this->newOrderTotals['discountTotal'] = ($this->newOrderTotals['discountTotal'] === 0.0) ? $this->newOrderTotals['discountTotal'] : -1 * $this->newOrderTotals['discountTotal'];
        $this->newOrderTotals['baseDiscountTotal'] = ($this->newOrderTotals['baseDiscountTotal'] === 0.0) ? $this->newOrderTotals['baseDiscountTotal'] : -1 * $this->newOrderTotals['baseDiscountTotal'];

        if ($orderCurrentDiscountAmount !== $this->newOrderTotals['discountTotal']) {
            $this->writeChanges(
                self::SECTION_ITEMS,
                $logOfChanges,
                'order_discount',
                'Order Discount',
                (string)$orderCurrentDiscountAmount,
                (string)$this->newOrderTotals['discountTotal']
            );
            $couponCode = $this->quote->getCouponCode();
            $this->order->setCouponCode($couponCode);
            $this->order->setDiscountAmount($this->newOrderTotals['discountTotal']);
        }

        if ($orderCurrentBaseDiscountAmount !== $this->newOrderTotals['baseDiscountTotal']) {
            $this->writeChanges(
                self::SECTION_ITEMS,
                $logOfChanges,
                'order_discount',
                'Order Discount',
                (string)$orderCurrentBaseDiscountAmount,
                (string)$this->newOrderTotals['baseDiscountTotal']
            );

            if (!$this->order->getCouponCode()) {
                $couponCode = $this->quote->getCouponCode();
                $this->order->setCouponCode($couponCode);
            }

            $this->order->setBaseDiscountAmount($this->newOrderTotals['baseDiscountTotal']);
        }

        $this->newOrderTotals['taxTotal'] += (float)$this->order->getShippingTaxAmount();

        $this->simpleOrderFieldChange('getTaxAmount', $logOfChanges, $this->newOrderTotals['taxTotal']);

        $this->newOrderTotals['baseTaxTotal'] += (float)$this->order->getBaseShippingTaxAmount();

        $this->simpleOrderFieldChange('getBaseTaxAmount', $logOfChanges, $this->newOrderTotals['baseTaxTotal']);

        $this->order->setSubtotalInclTax(
            (float)$this->order->getSubtotal()
            + (float)$this->order->getTaxAmount()
            - (float)$this->order->getShippingTaxAmount()
        );

        $this->order->setBaseSubtotalInclTax(
            (float)$this->order->getBaseSubtotal()
            + (float)$this->order->getBaseTaxAmount()
            - (float)$this->order->getBaseShippingTaxAmount()
        );

        // Grand total = Subtotal(excl tax) + Shipping(excl tax) + Tax
        $this->order->setGrandTotal(
            (float)$this->order->getSubtotal()
            + (float)$this->order->getShippingAmount()
            + (float)$this->order->getTaxAmount()
            + (float)$this->order->getDiscountAmount()
        );

        $this->order->setBaseGrandTotal(
            (float)$this->order->getBaseSubtotal()
            + (float)$this->order->getBaseShippingAmount()
            + (float)$this->order->getBaseTaxAmount()
            + (float)$this->order->getBaseDiscountAmount()
        );
    }

    /**
     * @param OrderItem $orderItem
     * @param $quoteItem
     * @param string $getMethod
     * @param array $logOfChanges
     * @param $currentValue
     * @param $newValue
     * @return float|mixed
     */
    protected function simpleOrderFieldChange(string $getMethod, array &$logOfChanges, $newValue)
    {

        $currentValue = $this->order->{$getMethod}();
        if ($currentValue != $newValue) {
            $nameOfField = str_replace('get', '', $getMethod);
            $this->writeChanges(
                self::SECTION_ITEMS,
                $logOfChanges,
                'Order' . $nameOfField,
                'Order '. $nameOfField,
                (string)$currentValue,
                (string)$newValue
            );

            $setMethod = str_replace('get', 'set', $getMethod);
            $this->order->{$setMethod}($newValue);

            return $newValue;
        }

        return $currentValue;
    }

    /**
     * @return void
     * @throws \Magento\CatalogInventory\Model\StockStateException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function addNewItemsToOrder(): void
    {
        if (count($this->quoteItemsMap)) {
            $resolvedNewItems = $this->resolveItems($this->quoteItemsMap);

            $items = $this->productQty->getProductQty($this->quoteItemsMap);
            $websiteId = (int)$this->order->getStore()->getWebsiteId();
            $this->stockManager->registerProductsSale($items, $websiteId);

            $itemsTotal = $this->calculateItemsTotal($resolvedNewItems);

            $this->newOrderTotals['subTotal'] += $itemsTotal['itemsTotal'];
            $this->newOrderTotals['baseSubTotal'] += $itemsTotal['baseItemsTotal'];
            $this->newOrderTotals['subTotalInclTax'] += $itemsTotal['itemsTotalInclTax'];
            $this->newOrderTotals['baseSubTotalInclTax'] += $itemsTotal['baseItemsTotalInclTax'];
            $this->newOrderTotals['taxTotal'] += $itemsTotal['itemsTaxTotal'];
            $this->newOrderTotals['baseTaxTotal'] += $itemsTotal['itemsBaseTaxTotal'];
            $this->newOrderTotals['discountTotal'] += $itemsTotal['itemsDiscountTotal'];
            $this->newOrderTotals['baseDiscountTotal'] += $itemsTotal['itemsBaseDiscountTotal'];

            $this->order->setItems($resolvedNewItems);
        }
    }

    /**
     * @param $orderItem
     * @return bool
     */
    protected function updateTaxItem($orderItem): bool
    {
        $orderTaxItem = $this->getOrderItemAppliedTaxes()[(int)$orderItem->getId()] ?? null;
        if ($orderTaxItem) {
            if ('product' === ($orderTaxItem['taxable_item_type'] ?? '')) {
                $connection = $this->getConnection();
                $tableName = $this->resourceConnection->getTableName('sales_order_tax_item');

                $taxItemData =
                    [
                        'tax_percent' => $orderItem->getTaxPercent(),
                        'amount' => $orderItem->getTaxAmount(),
                        'base_amount' => $orderItem->getBaseTaxAmount(),
                        'real_amount' => $orderItem->getTaxAmount(),
                        'real_base_amount' => $orderItem->getBaseTaxAmount(),
                    ];

                try {
                    $connection->update($tableName, $taxItemData, ['item_id = ?' => ($orderItem['item_id'] ?? 0)]);
                } catch (\Exception $e) {
                    $this->logger->debug(__('Magefan Order Edit ERROR: while updating tax item,namely: ') . $e->getMessage());
                    return false;
                }
            }
        } else {
            $orderTaxId = $this->taxManager->getOrderTaxId((int)$this->order->getId());
            if ($orderTaxId) {
                $this->missingOrderTaxItems[] =
                    [
                        'tax_id' => $orderTaxId,
                        'item_id' => $orderItem->getId(),
                        'tax_percent' => $orderItem->getTaxPercent(),
                        'amount' => $orderItem->getTaxAmount(),
                        'base_amount' => $orderItem->getBaseTaxAmount(),
                        'real_amount' => $orderItem->getTaxAmount(),
                        'real_base_amount' => $orderItem->getBaseTaxAmount(),
                        'taxable_item_type' => 'product'
                    ];
            }
        }


        return true;
    }

    /**
     * @return bool
     */
    protected function updateOrderTax(): bool
    {
        if ($taxRateId = (int)$this->quote->getData('mf_tax_rate_id')) {
            $connection = $this->getConnection();

            $select = $connection->select()->from(
                ['tax_rate' =>  $this->resourceConnection->getTableName('tax_calculation_rate')]
            )->where(
                'tax_rate.tax_calculation_rate_id = ?',
                $taxRateId
            );

            $data =  $connection->fetchAll($select);
            if (!isset($data[0])) {
                $this->logger->debug(__('Magefan Order Edit ERROR: while getting order tax'));
                return false;
            }

            $taxData  =
                [
                    'code' => $data[0]['code'],
                    'title' => $data[0]['code'],
                    'percent' => $data[0]['rate'],
                    'amount' => $this->order->getTaxAmount(),
                    'base_amount' => $this->order->getBaseTaxAmount(),
                    'base_real_amount' => $this->order->getBaseTaxAmount()
                ];

            try {
                $connection->update($this->resourceConnection->getTableName('sales_order_tax'), $taxData, ['order_id = ?' => $this->order->getId()]);
            } catch (\Exception $e) {
                $this->logger->debug(__('Magefan Order Edit ERROR: while updating order tax,namely: ') . $e->getMessage());
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function insertMissingOrderTaxItems(): bool
    {
        if (isset($this->missingOrderTaxItems)) {
            $connection = $this->getConnection();
            $tableName = $this->resourceConnection->getTableName('sales_order_tax_item');

            try {
                $this->connection->insertMultiple($tableName, $this->missingOrderTaxItems);
            } catch (\Exception $e) {
                $this->logger->debug(__('Magefan Order Edit ERROR: while inserting missing order tax items,namely: ') . $e->getMessage());
                return false;
            }
        }

        return true;
    }

    protected function deleteOrderTaxItemsWithNoItemId(): bool
    {
        if ($orderTaxId = $this->taxManager->getOrderTaxId((int)$this->order->getId())) {
            $connection = $this->getConnection();

            $connection->delete(
                $this->resourceConnection->getTableName('sales_order_tax_item'),
                [
                    'tax_id = ?' => $orderTaxId,
                    'item_id IS NULL',
                    'taxable_item_type = ?' => 'product'
                ]
            );

            return true;
        }

        $this->logger->debug(__('Magefan Order Edit ERROR: while deleting Order Tax Items With No Item Id: '));
        return false;
    }

    protected function deleteOrderTaxItemsWithTypeFee(): bool
    {
        if ($orderTaxId = $this->taxManager->getOrderTaxId((int)$this->order->getId())) {
            $connection = $this->getConnection();

            $connection->delete(
                $this->resourceConnection->getTableName('sales_order_tax_item'),
                [
                    'tax_id = ?' => $orderTaxId,
                    'taxable_item_type = ?' => 'fee'
                ]
            );

            return true;
        }

        $this->logger->debug(__('Magefan Order Edit ERROR: while deleting Order Tax Items With Type Fee: '));
        return false;
    }

    /**
     * @return array
     */
    protected function getOrderItemAppliedTaxes(): array
    {
        if (!isset($this->orderItemAppliedTaxes) && isset($this->order)) {
            $orderItemAppliedTaxes = $this->orderItemTaxFactory->create()->getTaxItemsByOrderId((int)$this->order->getId());

            $this->orderItemAppliedTaxes = [];
            foreach ($orderItemAppliedTaxes as $orderItemAppliedTax) {
                $this->orderItemAppliedTaxes[$orderItemAppliedTax['item_id']] = $orderItemAppliedTax;
            }
        }

        return $this->orderItemAppliedTaxes;
    }

    /**
     * @return mixed
     */
    protected function getConnection()
    {
        if (!isset($this->connection)) {
            $this->connection = $this->resourceConnection->getConnection();
        }

        return $this->connection;
    }

    /**
     * @param Order $order
     * @return array
     */
    protected function getInvoicesOrderItemsIds(Order $order): array
    {

        if (is_null($this->invoicesOrderItemsIds)) {
            $invoices = $order->getInvoiceCollection();

            $this->invoicesOrderItemsIds = [];

            foreach ($invoices as $invoice) {
                $invoiceItems = $invoice->getItems();
                foreach ($invoiceItems as $invoiceItem) {
                    $this->invoicesOrderItemsIds[$invoiceItem->getOrderItemId()] = $invoiceItem;
                }
            }
        }

        return $this->invoicesOrderItemsIds;
    }

    /**
     * @param OrderItem $orderItem
     * @param $quoteItem
     * @param string $getMethod
     * @param array $logOfChanges
     * @param $currentValue
     * @param $newValue
     * @return float|mixed
     */
    protected function simpleItemFieldChange(
        OrderItem $orderItem,
                  $quoteItem,
        string $getMethod,
        array &$logOfChanges,
                  $currentValue = null,
                  $newValue = null
    ) {

        $currentValue = $currentValue ?: (float)$orderItem->{$getMethod}();
        $newValue = $newValue ?: (float)$quoteItem->{$getMethod}();

        if ($currentValue !== $newValue) {
            $nameOfField = str_replace('get', '', $getMethod);
            $this->writeChanges(
                self::SECTION_ITEMS,
                $logOfChanges,
                $nameOfField . $orderItem->getId(),
                'item(' . $orderItem->getSku() . ') '. $nameOfField,
                (string)$currentValue,
                (string)$newValue
            );

            $setMethod = str_replace('get', 'set', $getMethod);
            $orderItem->{$setMethod}($newValue);

            return $newValue;
        }

        return $currentValue;
    }

    /**
     * @param array $quoteItems
     * @return float[]
     */
    protected function calculateItemsTotal(array $quoteItems): array
    {
        $itemsTotal = ['baseItemsTotal' => 0.0, 'itemsTotal' => 0.0, 'baseItemsTotalInclTax' => 0.0, 'itemsTotalInclTax' => 0.0,'itemsBaseTaxTotal' => 0.0, 'itemsTaxTotal' => 0.0,
            'itemsBaseDiscountTotal' => 0.0, 'itemsDiscountTotal' => 0.0];

        foreach ($quoteItems as $quoteItem) {
            if ('bundle' === $quoteItem->getProductType()) {
                continue;
            }

            $itemsTotal['baseItemsTotal'] += (float)$quoteItem->getBasePrice() * ((float)$quoteItem->getQty() ? : $quoteItem->getQtyOrdered());
            $itemsTotal['baseItemsTotalInclTax'] += (float)$quoteItem->getBasePriceInclTax() * ((float)$quoteItem->getQty() ? : $quoteItem->getQtyOrdered());
            $itemsTotal['itemsTotal'] += $this->getPriceForQuoteItem($quoteItem) * ((float)$quoteItem->getQty() ? : $quoteItem->getQtyOrdered());
            $itemsTotal['itemsTotalInclTax'] += (float)$quoteItem->getPriceInclTax() * ((float)$quoteItem->getQty() ? : $quoteItem->getQtyOrdered());
            $itemsTotal['itemsBaseTaxTotal'] += (float)$quoteItem->getBaseTaxAmount();
            $itemsTotal['itemsTaxTotal'] += (float)$quoteItem->getTaxAmount();
            $itemsTotal['itemsBaseDiscountTotal'] += (float)$quoteItem->getBaseDiscountAmount();
            $itemsTotal['itemsDiscountTotal'] += (float)$quoteItem->getDiscountAmount();
        }

        return $itemsTotal;
    }

    /**
     * @param  array $quote
     * @return array
     */
    protected function resolveItems(array $quoteItems) : array
    {
        $orderItems = [];

        foreach ($quoteItems as $quoteItem) {
            $itemId = $quoteItem->getId();

            if (!empty($orderItems[$itemId])) {
                continue;
            }

            $parentItemId = $quoteItem->getParentItemId();
            /**
             * @var \Magento\Quote\Model\ResourceModel\Quote\Item $parentItem
             */
            if ($parentItemId && !isset($orderItems[$parentItemId])) {
                $orderItems[$parentItemId] = $this->toOrderItem->convert(
                    $quoteItem->getParentItem(),
                    ['parent_item' => null]
                );
            }

            $parentItem = $orderItems[$parentItemId] ?? null;
            $orderItems[$itemId] = $this->toOrderItem->convert($quoteItem, ['parent_item' => $parentItem]);
        }

        return array_values($orderItems);
    }

    /**
     * @param Quote $quote
     * @return array
     */
    protected function prepareItems(Quote $quote): array
    {
        if (is_null($this->quoteItemsMap)) {
            $this->quoteItemsMap = [];

            foreach ($quote->getAllItems() as $quoteItem) {
                $this->quoteItemsMap[(int)$quoteItem->getId()] = $quoteItem;
            }
        }

        return $this->quoteItemsMap;
    }

    /**
     * @param $orderItem
     * @param $quoteItemId
     * @param $childOrderItem
     * @return void
     */
    protected function editOrderItemWithQuoteItem($orderItem, $quoteItemId, &$logOfChanges, $childOrderItem = null): void
    {
        $quoteItem = $this->quoteItemsMap[$quoteItemId] ?? null;

        if ($quoteItem) {
            if (self::SKIP_PARENT_ITEM_ID == $orderItem->getParentItemId()) {
                $orderItem->setParentItemId(null);
            }

            if ($childOrderItem && self::SKIP_PARENT_ITEM_ID == $childOrderItem->getParentItemId()) {
                $childOrderItem->setParentItemId($orderItem->getId());
            }

            $composites = $this->getCompositeItems($this->order);
            $qtyNew = (float)$quoteItem->getQty();
            $discountTotalOfNewItem = (float)$quoteItem->getTotalDiscountAmount();
            $baseDiscountTotalOfNewItem = (float)$quoteItem->getData('base_discount_amount');

            if ('bundle' !== $orderItem->getProductType()) {
                $quoteItem = $this->resolveItems([$quoteItem])[0];
            }

            if ($childOrderItem) {
                unset($this->quoteItemsMap[$childOrderItem->getQuoteItemId()]);
            }

            unset($this->quoteItemsMap[$quoteItemId]);

            $qtyOld = (float)$orderItem->getQtyOrdered();

            if ($qtyOld !== $qtyNew) {

                $newProductId = (int)$quoteItem->getProduct()->getId();

                $this->stockManager->backItemQty($newProductId, $qtyOld - $qtyNew, $this->order->getStore()->getWebsiteId());

                $this->writeChanges(
                    self::SECTION_ITEMS,
                    $logOfChanges,
                    'qty' . $orderItem->getId(),
                    'item(' . $orderItem->getSku() . ') qty',
                    (string)$qtyOld,
                    (string)$qtyNew
                );

                $orderItem->setQtyOrdered($qtyNew);
            }

            $this->updateSimpleOrderItemTotals($orderItem, $quoteItem, $logOfChanges, ['qtyNew' => $qtyNew, 'discountTotalOfNewItem' => $discountTotalOfNewItem,
                'baseDiscountTotalOfNewItem' => $baseDiscountTotalOfNewItem]);

            $invoiceItem = $this->getInvoicesOrderItemsIds($this->order)[$orderItem->getId()] ?? null;

            $priceOfNewItem = (float)$quoteItem->getPrice();
            $priceOfCurrentItem = (float)$orderItem->getPrice();

            if ($priceOfCurrentItem !== $priceOfNewItem) {
                if ((float)$orderItem->getOriginalPrice() === 0.0) {
                    $orderItem->setOriginalPrice($priceOfCurrentItem);
                }

                $this->writeChanges(
                    self::SECTION_ITEMS,
                    $logOfChanges,
                    'price' . $orderItem->getId(),
                    'item (' . $orderItem->getSku() . ') Price',
                    (string)$priceOfCurrentItem,
                    (string)$priceOfNewItem
                );

                if ($invoiceItem) {
                    $invoiceItem->setPrice($priceOfNewItem);
                    ;
                }
                $orderItem->setPrice($priceOfNewItem);
            }

            $this->simpleItemFieldChange($orderItem, $quoteItem, 'getPriceInclTax', $logOfChanges);

            $basePriceOfNewItem = (float)$quoteItem->getBasePrice();
            $basePriceOfCurrentItem = (float)$orderItem->getBasePrice();

            if ($basePriceOfCurrentItem !== $basePriceOfNewItem) {
                if ((float)$orderItem->getBaseOriginalPrice() === 0.0) {
                    $orderItem->setBaseOriginalPrice($basePriceOfCurrentItem);
                }

                $this->writeChanges(
                    self::SECTION_ITEMS,
                    $logOfChanges,
                    'price' . $orderItem->getId(),
                    'item (' . $orderItem->getSku() . ') Price',
                    (string)$basePriceOfCurrentItem,
                    (string)$basePriceOfNewItem
                );

                if ($invoiceItem) {
                    $invoiceItem->setBasePrice($basePriceOfNewItem);
                    ;
                }
                $orderItem->setBasePrice($basePriceOfNewItem);
            }

            $this->simpleItemFieldChange($orderItem, $quoteItem, 'getBasePriceInclTax', $logOfChanges);

            try {
                if ($invoiceItem) {
                    $this->invoiceItemRepository->save($invoiceItem);
                }
                $this->orderItemRepository->save($orderItem);
            } catch (NoSuchEntityException $e) {
            }
        }
    }

    /**
     * @param $orderItem
     * @param $quoteItem
     * @param $additionalFields
     * @return void
     */
    protected function updateSimpleOrderItemTotals($orderItem, $quoteItem, &$logOfChanges, array $additionalFields = []): void
    {
        $this->newOrderTotals['subTotal'] += $this->simpleItemFieldChange(
            $orderItem,
            $quoteItem,
            'getRowTotal',
            $logOfChanges,
            null,
            $this->getPriceForQuoteItem($quoteItem) * $additionalFields['qtyNew']
        );

        $this->newOrderTotals['subTotalInclTax'] += $this->simpleItemFieldChange(
            $orderItem,
            $quoteItem,
            'getRowTotalInclTax',
            $logOfChanges,
            null,
            (float)$quoteItem->getPriceInclTax() * $additionalFields['qtyNew']
        );

        $this->simpleItemFieldChange($orderItem, $quoteItem, 'getTaxPercent', $logOfChanges);

        $this->newOrderTotals['baseSubTotal'] += $this->simpleItemFieldChange(
            $orderItem,
            $quoteItem,
            'getBaseRowTotal',
            $logOfChanges,
            null,
            (float)$quoteItem->getBasePrice() * $additionalFields['qtyNew']
        );
        $this->newOrderTotals['baseSubTotalInclTax'] += $this->simpleItemFieldChange(
            $orderItem,
            $quoteItem,
            'getBaseRowTotalInclTax',
            $logOfChanges,
            null,
            (float)$quoteItem->getBaseRowTotalInclTax() * $additionalFields['qtyNew']
        );

        $this->newOrderTotals['taxTotal'] += $this->simpleItemFieldChange($orderItem, $quoteItem, 'getTaxAmount', $logOfChanges);
        $this->newOrderTotals['baseTaxTotal'] += $this->simpleItemFieldChange($orderItem, $quoteItem, 'getBaseTaxAmount', $logOfChanges);

        $this->newOrderTotals['discountTotal']
            += $this->simpleItemFieldChange($orderItem, $quoteItem, 'getDiscountAmount', $logOfChanges, null, $additionalFields['discountTotalOfNewItem']);
        $this->newOrderTotals['baseDiscountTotal']
            += $this->simpleItemFieldChange($orderItem, $quoteItem, 'getBaseDiscountAmount', $logOfChanges, null, $additionalFields['baseDiscountTotalOfNewItem']);
    }

    protected function getPriceForQuoteItem($quoteItem): float
    {
        if ($this->config->displayPricesInCatalogInclTax()) {
            return (($this->quoteItem->getPriceInclTax()*100) / (100+ $quoteItem->getTaxPercent()));
        }

        return (float)$quoteItem->getPrice();
    }

    /**
     * @param $order
     * @return array
     */
    private function getCompositeItems($order): array
    {
        if (is_null($this->composites)) {
            $this->composites = [];

            foreach ($order->getAllItems() as $orderItem) {
                if (in_array($orderItem->getProductType(), ['configurable', 'bundle'])) {
                    $this->composites[$orderItem->getId()] = $orderItem;
                }
            }
        }

        return $this->composites;
    }

    /**
     * @param $orderItem
     * @param $productId
     * @param $childOrderItem
     * @return void
     */
    private function removeOrderItem($orderItem, $productId, $childOrderItem = null): void
    {
        $websiteId = $this->order->getStore()->getWebsiteId();

        try {
            $itemOrderQty = (float)$orderItem->getQtyOrdered();
            $this->quoteItem->setQty($itemOrderQty);
            $items = $this->productQty->getProductQty([$this->quoteItem]);

            $this->stockManager->revertProductsSale($items, $websiteId);

            $this->stockManager->backItemQty($productId, $itemOrderQty, $websiteId);

            if (!isset($this->getInvoicesOrderItemsIds($this->order)[$orderItem->getId()])) {
                $this->orderItemRepository->delete($orderItem);
            } else {
                $orderItem->setParentItemId(self::SKIP_PARENT_ITEM_ID);
                $this->orderItemRepository->save($orderItem);
            }

            if ($childOrderItem) {
                if (!isset($this->getInvoicesOrderItemsIds($this->order)[$childOrderItem->getId()])) {
                    $this->orderItemRepository->delete($childOrderItem);
                } else {
                    $childOrderItem->setParentItemId(self::SKIP_PARENT_ITEM_ID);

                    $this->orderItemRepository->save($childOrderItem);
                }
            }
        } catch (NoSuchEntityException $e) {

        }
    }
}
