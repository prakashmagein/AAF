<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Model\Order;

use Magefan\OrderEdit\Model\Quote\TaxManager;
use Magento\Sales\Model\Order;
use Magento\Quote\Model\Quote;
use Magento\Payment\Api\PaymentMethodListInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\App\Emulation;

class UpdateOrderPaymentMethod extends AbstractUpdateOrder
{
    /**
     * @var PaymentMethodListInterface
     */
    protected $paymentMethodList;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var Emulation
     */
    protected $emulation;

    /**
     * @param StoreManagerInterface $storeManager
     * @param TaxManager $taxManager
     * @param PaymentMethodListInterface $paymentMethodList
     * @param Emulation $emulation
     * @param ResourceConnection|null $resourceConnection
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        TaxManager $taxManager,
        PaymentMethodListInterface $paymentMethodList,
        Emulation $emulation,
        ResourceConnection $resourceConnection = null
    ) {
        $this->paymentMethodList = $paymentMethodList;
        $this->emulation = $emulation;
        $this->resourceConnection = $resourceConnection ?:  \Magento\Framework\App\ObjectManager::getInstance()
            ->get(ResourceConnection::class);

        parent::__construct($storeManager, $taxManager);
    }

    /**
     * @param Order $order
     * @param array $logOfChanges
     * @param Quote|null $quote
     * @param string|null $orderNewShippingMethod
     * @return bool
     */
    public function execute(Order $order, array &$logOfChanges, Quote $quote = null, string $orderNewPaymentMethod = null, string $poNumber = ''): bool
    {
        $isValidPaymentMethod = false;
        $this->emulation->startEnvironmentEmulation($order->getStoreId());
        $activePaymentMethodList = $this->paymentMethodList->getActiveList((int)$order->getStoreId());

        foreach ($activePaymentMethodList as $activePaymentMethod) {
            if ($orderNewPaymentMethod === $activePaymentMethod->getCode()) {
                $isValidPaymentMethod = true;
                break;
            }
        }

        if (!$isValidPaymentMethod) {
            $this->emulation->stopEnvironmentEmulation();
            return false;
        }

        $orderCurrentPaymentMethod = $order->getPayment()->getMethod();

        if ($poNumber) {
            $order->getPayment()->setPoNumber($poNumber);
        }

        if ($orderCurrentPaymentMethod !== $orderNewPaymentMethod) {

            $this->writeChanges(
                self::SECTION_PAYMENT_METHOD,
                $logOfChanges,
                'method',
                'Payment Method',
                $orderCurrentPaymentMethod,
                $orderNewPaymentMethod
            );

            $order->getPayment()->setMethod($orderNewPaymentMethod);
            $quote->getPayment()->setMethod($orderNewPaymentMethod);
        }

        //Syncronize custom/unknown(for us) payment fields between quote and order
        if (($orderPayment = $order->getPayment())
            && ($quotePayment = $quote->getPayment())) {

            foreach ($quotePayment->getData() as $k => $v) {
                $orderPayment->setData($k, $v);
            }
        }

        $this->emulation->stopEnvironmentEmulation();
        return true;
    }

    /**
     * @param $order
     * @param $row
     * @param bool $add
     * @return void
     */
    protected function updateGrandTotalsWithFee($order, $row, bool $add = true): void
    {
        $amount = (float)($row['amount'] ?? 0.0);
        $taxAmount = (float)($row['tax_amount'] ?? 0.0);

        $baseAmount = (float)($row['base_amount'] ?? 0.0);
        $baseTaxAmount = (float)($row['base_tax_amount'] ?? 0.0);

        if ($add) {
            $order->setGrandTotal($order->getGrandTotal() + ($amount + $taxAmount));
            $order->setBaseGrandTotal($order->getBaseGrandTotal() + ($baseAmount + $baseTaxAmount));
        } else {
            $order->setGrandTotal($order->getGrandTotal() - ($amount + $taxAmount));
            $order->setBaseGrandTotal($order->getBaseGrandTotal() - ($baseAmount + $baseTaxAmount));
        }
    }
}
