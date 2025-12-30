<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Model\Order;

use Magento\Framework\App\ResourceConnection;
use Magefan\OrderEdit\Block\Adminhtml\Order\Edit\Form;
use Magento\Framework\App\RequestInterface;

class AmastyCashOnDeliveryHandler
{
    const CASH_ON_DELIVERY = 'cashondelivery';

    const AMASTY_CASH_ON_DELIVERY_FEE_ORDER_TABLE = 'amasty_cash_on_delivery_fee_order';

    protected $isAmastyCashOnDeliveryFeeAdded;

    protected $order;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @param ResourceConnection $resourceConnection
     * @param RequestInterface $request
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        RequestInterface $request
    )
    {
        $this->resourceConnection = $resourceConnection;
        $this->request = $request;
    }

    /**
     * @param $order
     * @return void
     */
    public function execute($order): void
    {
        if ($this->isAmastyCashOnDeliveryFeeRepresented()) {
            $this->order = $order;
            $orderCurrentPaymentMethod = $order->getPayment()->getMethod();

            $amastyFeeAdded = false;
            if (self::CASH_ON_DELIVERY === $orderCurrentPaymentMethod && !$this->isAmastyCashOnDeliveryFeeAdded()) {
                $this->addAmastyCashOnDeliveryFee();
                $amastyFeeAdded = true;
            } elseif (self::CASH_ON_DELIVERY !== $orderCurrentPaymentMethod && $this->isAmastyCashOnDeliveryFeeAdded()) {
                $this->updateGrandTotalsWithFee(false);
                $this->deleteAmastyCashOnDeliveryFee();
            }

            if ($amastyFeeAdded || $this->orderTotalsEdited()) {
                $this->updateGrandTotalsWithFee();
            }
        }
    }

    /**
     * @param bool $add
     * @return void
     */
    protected function updateGrandTotalsWithFee(bool $add = true): void
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName(self::AMASTY_CASH_ON_DELIVERY_FEE_ORDER_TABLE);

        $select = $connection->select()->from($tableName)->where('order_id = ?', $this->order->getId());
        $row = $connection->fetchRow($select);

        $amount = (float)($row['amount'] ?? 0.0);
        $taxAmount = (float)($row['tax_amount'] ?? 0.0);

        $baseAmount = (float)($row['base_amount'] ?? 0.0);
        $baseTaxAmount = (float)($row['base_tax_amount'] ?? 0.0);

        if ($add) {
            $this->order->setTaxAmount($this->order->getTaxAmount() + $taxAmount);
            $this->order->setBaseTaxAmount($this->order->getBaseTaxAmount() + $baseTaxAmount);

            $this->order->setGrandTotal($this->order->getGrandTotal() + ($amount + $taxAmount));
            $this->order->setBaseGrandTotal($this->order->getBaseGrandTotal() + ($baseAmount + $baseTaxAmount));
        } else {
            $this->order->setTaxAmount($this->order->getTaxAmount() - $taxAmount);
            $this->order->setBaseTaxAmount($this->order->getBaseTaxAmount() - $baseTaxAmount);

            $this->order->setGrandTotal($this->order->getGrandTotal() - ($amount + $taxAmount));
            $this->order->setBaseGrandTotal($this->order->getBaseGrandTotal() - ($baseAmount + $baseTaxAmount));
        }
    }

    /**
     * @return bool
     */
    protected function orderTotalsEdited(): bool
    {
        return in_array((int)$this->request->getParam('form_type'),
            [Form::SHIPPING_METHOD_EDIT_FORM, Form::ALL_TYPES_EDIT_FORM, Form::ITEMS_ORDERED_EDIT_FORM]
        );
    }

    /**
     * @return bool
     */
    protected function isAmastyCashOnDeliveryFeeRepresented(): bool
    {
        $connection = $this->resourceConnection->getConnection();

        $tableName = $this->resourceConnection->getTableName(self::AMASTY_CASH_ON_DELIVERY_FEE_ORDER_TABLE);
        return $connection->isTableExists($tableName);
    }

    /**
     * @return bool
     */
    protected function isAmastyCashOnDeliveryFeeAdded(): bool
    {
        if (!isset($this->isAmastyCashOnDeliveryFeeAdded)) {
            $connection = $this->resourceConnection->getConnection();
            $tableName = $this->resourceConnection->getTableName(self::AMASTY_CASH_ON_DELIVERY_FEE_ORDER_TABLE);

            $select = $connection->select()->from($tableName)->where('order_id = ?', $this->order->getId());
            $this->isAmastyCashOnDeliveryFeeAdded = isset($connection->fetchRow($select)['base_amount']);
        }

        return $this->isAmastyCashOnDeliveryFeeAdded;
    }

    /**
     * @return bool
     */
    protected function addAmastyCashOnDeliveryFee(): void
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName(self::AMASTY_CASH_ON_DELIVERY_FEE_ORDER_TABLE);

        $select = $connection->select()->from($tableName)->limit(1);
        $result = $connection->fetchRow($select);

        if (isset($result['entity_id'])) {
            $newFee = [
                'order_id' => $this->order->getId(),
                'amount' => $result['amount'],
                'base_amount' => $result['base_amount'],
                'tax_amount'  => $result['tax_amount'],
                'base_tax_amount'  => $result['base_tax_amount']
            ];

            try {
                $connection->insert($tableName, $newFee);
            } catch (\Exception $e) {
            }
        }
    }

    /**
     * @return void
     */
    protected function deleteAmastyCashOnDeliveryFee(): void
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName(self::AMASTY_CASH_ON_DELIVERY_FEE_ORDER_TABLE);

        $connection->delete(
            $tableName,
            ['order_id = ?' => $this->order->getId()]
        );
    }
}