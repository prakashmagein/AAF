<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Model\Quote;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface;

class TaxManager
{
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Manager
     */
    protected $quoteManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    protected $orderTaxId;

    protected $taxPercent;

    /**
     * @param ResourceConnection $resourceConnection
     * @param Registry $registry
     * @param Manager $quoteManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        Registry $registry,
        Manager $quoteManager,
        LoggerInterface $logger
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->registry = $registry;
        $this->quoteManager = $quoteManager;
        $this->logger = $logger;
    }

    /**
     * @param int $taxRateId
     */
    public function addTaxRate(int $taxRateId = 0): void
    {
        $this->quoteManager->_getQuote()->setData('mf_tax_rate_id', $taxRateId);

        $data = [];
        if ($taxRateId) {
            $connection = $this->resourceConnection->getConnection();
            $select = $connection->select()
                ->from(
                    ['ce' => $this->resourceConnection->getTableName('tax_calculation')]
                )
                ->joinLeft(
                    ['sec' => $this->resourceConnection->getTableName('tax_calculation_rate')],
                    'ce.tax_calculation_rate_id = sec.tax_calculation_rate_id'
                )
                ->where(
                    'ce.tax_calculation_rate_id  = ?',
                    $taxRateId
                );

            $data = $connection->fetchRow($select);
        }

        $this->registry->register('mf_order_edit_tax_region_id', $data['tax_region_id'] ?? 0);
        $this->registry->register('mf_order_edit_customer_tax_class_id', $data['customer_tax_class_id'] ?? 0);
        $this->registry->register('mf_order_edit_product_tax_class_id', $data['product_tax_class_id'] ?? 0);
        $this->registry->register('mf_order_edit_tax_country_id', (string)($data['tax_country_id'] ?? 0));
    }

    /**
     * @param $order
     * @return void
     */
    public function updateShippingTaxItem($order, $quote): void
    {
        $shippingTaxItemData = [
            'tax_id' => $this->getOrderTaxId((int)$order->getId()),
            'tax_percent' => $this->getTaxPercent($order),
            'amount' => $quote->getShippingAddress()->getShippingTaxAmount(),
            'base_amount' => $quote->getShippingAddress()->getBaseShippingTaxAmount(),
            'real_amount' => $quote->getShippingAddress()->getShippingTaxAmount(),
            'real_base_amount' => $quote->getShippingAddress()->getBaseShippingTaxAmount(),
        ];

        if ($existingShippingTaxItem  = $this->getExistingShippingTaxItem((int)$order->getId())) {
            $this->updateExistingShippingTaxItem($existingShippingTaxItem, $shippingTaxItemData);
        } else {
            $this->createShippingTaxItem($shippingTaxItemData);
        }

        $this->updateOrderTax($order, $quote);
    }

    /**
     * @param int $orderId
     * @return int
     */
    public function getOrderTaxId(int $orderId): int
    {
        if (!isset($this->orderTaxId)) {
            $connection = $this->resourceConnection->getConnection();

            $select = $connection->select()->from(
                ['tax' => $this->resourceConnection->getTableName('sales_order_tax')],
                [
                    'tax_id',
                ]
            )->where(
                'tax.order_id = ?',
                $orderId
            );

            $data =  $connection->fetchAll($select);

            $this->orderTaxId = (int)($data[0]['tax_id'] ?? 0);
        }

        return $this->orderTaxId;
    }

    /**
     * @return bool
     */
    public function updateOrderTax($order, $quote): bool
    {
        if ($taxRateId = (int)$quote->getData('mf_tax_rate_id')) {
            $connection = $this->resourceConnection->getConnection();

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
                    'amount' => $order->getTaxAmount(),
                    'base_amount' => $order->getBaseTaxAmount(),
                    'base_real_amount' => $order->getBaseTaxAmount()
                ];

            try {
                $connection->update($this->resourceConnection->getTableName('sales_order_tax'), $taxData, ['order_id = ?' => $order->getId()]);
            } catch (\Exception $e) {
                $this->logger->debug(__('Magefan Order Edit ERROR: while updating order tax,namely: ') . $e->getMessage());
                return false;
            }
        }

        return true;
    }

    /**
     * @param $order
     * @return float
     */
    protected function getTaxPercent($order): float
    {
        if (!isset($this->taxPercent)) {
            $this->taxPercent = 0.0;

            foreach ($order->getAllItems() as $orderItem) {
                $this->taxPercent = (float)$orderItem->getTaxPercent();
                break;
            }
        }

        return $this->taxPercent;
    }

    /**
     * @param $shippingTaxItem
     * @param $quote
     * @return bool
     */
    protected function updateExistingShippingTaxItem($existingShippingTaxItem, $shippingTaxItemData): bool
    {
        $tableName = $this->resourceConnection->getTableName('sales_order_tax_item');
        $connection = $this->resourceConnection->getConnection();

        try {
            $connection->update(
                $tableName,
                $shippingTaxItemData,
                [
                    'tax_id = ?' => ($existingShippingTaxItem['tax_id'] ?? 0),
                    'taxable_item_type = ?' => 'shipping'
                ]
            );
        } catch (\Exception $e) {
            $this->logger->debug(__('Magefan Order Edit ERROR: while updating shipping tax item,namely: ') . $e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * @param $shippingTaxItem
     * @param $quote
     * @return bool
     */
    protected function createShippingTaxItem($shippingTaxItemData): bool
    {
        $tableName = $this->resourceConnection->getTableName('sales_order_tax_item');
        $connection = $this->resourceConnection->getConnection();

        $shippingTaxItemData['taxable_item_type'] = 'shipping';
        try {
            $connection->insert($tableName, $shippingTaxItemData);
        } catch (\Exception $e) {
            $this->logger->debug(__('Magefan Order Edit ERROR: while creating shipping tax item,namely: ') . $e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * @param int $orderId
     * @return mixed
     */
    protected function getExistingShippingTaxItem(int $orderId)
    {
        $connection = $this->resourceConnection->getConnection();

        $select = $connection->select()
            ->from(
                ['ce' => $this->resourceConnection->getTableName('sales_order_tax')]
            )
            ->joinLeft(
                ['sec' => $this->resourceConnection->getTableName('sales_order_tax_item')],
                'ce.tax_id = sec.tax_id'
            )
            ->where(
                'ce.order_id  = ?',
                $orderId
            )
            ->where(
                'sec.taxable_item_type  = ?',
                'shipping'
            );

        //Return false if it cannot find row,row otherwise
        return $connection->fetchRow($select);
    }
}
