<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Model\Order;

use Magento\Sales\Model\Order;
use Magento\Quote\Model\Quote;
use Magento\Shipping\Model\Config as ShippingConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magefan\OrderEdit\Model\Quote\TaxManager;
use Magefan\OrderEdit\Model\Quote\Manager as QuoteManager;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\App\Emulation;

class UpdateOrderShippingMethod extends AbstractUpdateOrder
{
    const AMASTY_METHOD_LABEL_TABLE = 'amasty_method_label';

    /**
     * @var ShippingConfig
     */
    protected $shipconfig;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var TaxManager
     */
    protected $taxManager;

    /**
     * @var QuoteManager
     */
    protected $quoteManager;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var Registry
     */
    protected $registry;

    protected $quote;

    protected $order;

    protected $priceForShippingMethod;

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
     * @param ShippingConfig $shipconfig
     * @param ScopeConfigInterface $scopeConfig
     * @param QuoteManager $quoteManager
     * @param PriceCurrencyInterface $priceCurrency
     * @param Registry $registry
     * @param Emulation $emulation
     * @param ResourceConnection|null $resourceConnection
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        TaxManager $taxManager,
        ShippingConfig $shipconfig,
        ScopeConfigInterface $scopeConfig,
        QuoteManager $quoteManager,
        PriceCurrencyInterface $priceCurrency,
        Registry $registry,
        Emulation $emulation,
        ResourceConnection $resourceConnection = null
    ) {
        $this->shipconfig = $shipconfig;
        $this->scopeConfig = $scopeConfig;
        $this->quoteManager = $quoteManager;
        $this->priceCurrency = $priceCurrency;
        $this->registry = $registry;
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
    public function execute(Order $order, array &$logOfChanges, Quote $quote = null, string $orderNewShippingMethod = null, $customShippingPrice = ''): bool
    {
        $this->quote = $quote;
        $this->order = $order;

        $this->emulation->startEnvironmentEmulation($order->getStoreId());
        $methodTitle = $this->validateShippingMethod($orderNewShippingMethod);

        if (!$methodTitle) {
            $this->emulation->stopEnvironmentEmulation();
            return false;
        }
        $order->setShippingDescription($methodTitle);

        $this->quote->setData('mf_custom_shipping_price', $customShippingPrice);

        $this->reCollectTotalsWithCustomShippingPrice($customShippingPrice);

        $this->updateOrderShippingAssignments($orderNewShippingMethod, $logOfChanges);

        $quote->getShippingAddress()->setShippingMethod($orderNewShippingMethod);

        $pricesForShippingMethod = $this->getPricesForShippingMethod($orderNewShippingMethod, $customShippingPrice);
        if (empty($pricesForShippingMethod)) {
            $this->emulation->stopEnvironmentEmulation();
            return false;
        }

        $this->collectTotals($pricesForShippingMethod);

        $this->taxManager->updateShippingTaxItem($order, $quote);

        $this->emulation->stopEnvironmentEmulation();
        return true;
    }

    /**
     * @return void
     */
    private function collectTotals($pricesForShippingMethod): void
    {
        $this->order->setShippingAmount($pricesForShippingMethod['price'] ?? 0);
        $this->order->setBaseShippingAmount($pricesForShippingMethod['basePrice'] ?? 0);

        $this->order->setShippingInclTax($this->quote->getShippingAddress()->getShippingInclTax());
        $this->order->setBaseShippingInclTax($this->quote->getShippingAddress()->getBaseShippingInclTax());

        $this->order->setTaxAmount($this->order->getTaxAmount() - $this->order->getShippingTaxAmount());
        $this->order->setBaseTaxAmount($this->order->getBaseTaxAmount() - $this->order->getBaseShippingTaxAmount());

        $this->order->setShippingTaxAmount($this->quote->getShippingAddress()->getShippingTaxAmount());
        $this->order->setBaseShippingTaxAmount($this->quote->getShippingAddress()->getBaseShippingTaxAmount());

        $this->order->setTaxAmount($this->order->getTaxAmount() + $this->order->getShippingTaxAmount());
        $this->order->setBaseTaxAmount($this->order->getBaseTaxAmount() + $this->order->getBaseShippingTaxAmount());

        // Grand total = Subtotal(excl tax) + Shipping(excl tax) + Tax
        $this->order->setGrandTotal(
            (float)$this->order->getSubtotal()
            + (float)$this->order->getShippingAmount()
            + (float)$this->order->getTaxAmount()
        );

        $this->order->setBaseGrandTotal(
            (float)$this->order->getBaseSubtotal()
            + (float)$this->order->getBaseShippingAmount()
            + (float)$this->order->getBaseTaxAmount()
        );
    }

    /**
     * @param $orderNewShippingMethod
     * @param array $logOfChanges
     * @return void
     */
    private function updateOrderShippingAssignments($orderNewShippingMethod, array &$logOfChanges): void
    {
        $orderCurrentShippingMethod = (string)$this->order->getShippingMethod();
        if ($orderCurrentShippingMethod !== $orderNewShippingMethod) {
            $this->writeChanges(
                self::SECTION_SHIPPING_METHOD,
                $logOfChanges,
                'shipping_method',
                'Shipping Method',
                $orderCurrentShippingMethod,
                $orderNewShippingMethod
            );

            $shippingAssignments = $this->order->getExtensionAttributes()->getShippingAssignments();

            if (!empty($shippingAssignments)) {
                array_shift($shippingAssignments)->getShipping()->setMethod($orderNewShippingMethod);
            }
        }
    }

    /**
     * @param $orderNewShippingMethod
     * @param string $customShippingPrice
     * @return array|float[]
     */
    private function getPricesForShippingMethod($orderNewShippingMethod = '', string $customShippingPrice = ''): array
    {
        if (!isset($this->priceForShippingMethodi)) {

            $pricesForShippingMethod = ['price' => 0.0, 'basePrice' => 0.0];
            if ('' !== $customShippingPrice) {
                $customShippingPrice = (float)$customShippingPrice;
                $baseCustomShippingPrice = $customShippingPrice;

                if ($customShippingPrice) {
                    $rate = $this->priceCurrency->convert($customShippingPrice, $this->quoteManager->_getQuote()->getStore()) / $customShippingPrice;
                    $baseCustomShippingPrice = $customShippingPrice / $rate;
                }

                $pricesForShippingMethod['basePrice'] = $baseCustomShippingPrice;
                $pricesForShippingMethod['price'] = $customShippingPrice;

                $this->priceForShippingMethod = $pricesForShippingMethod;
                return $this->priceForShippingMethod;
            }

            $shippingRateGroups = $this->quote->getShippingAddress()->getGroupedAllShippingRates();
            foreach ($shippingRateGroups as $rates) {
                foreach ($rates as $rate) {
                    if ($orderNewShippingMethod === $rate->getCode()) {
                        $pricesForShippingMethod['basePrice'] = (float)$rate->getPrice();
                        $pricesForShippingMethod['price'] = $this->quote->getShippingAddress()->getShippingAmount();

                        $this->priceForShippingMethod = $pricesForShippingMethod;
                        return $this->priceForShippingMethod;
                    }
                }
            }

            $this->priceForShippingMethod = [];
            return $this->priceForShippingMethod;
        }

        return $this->priceForShippingMethod;
    }

    /**
     * @return bool
     */
    private function isAmastyShippingMethodsRepresented(): bool
    {
        $connection = $this->resourceConnection->getConnection();

        $tableName = $this->resourceConnection->getTableName(self::AMASTY_METHOD_LABEL_TABLE);
        return $connection->isTableExists($tableName);
    }

    /**
     * @param string $orderNewShippingMethod
     * @return string
     */
    private function validateShippingMethod(string $orderNewShippingMethod): string
    {
        $methodTitle = '';
        $activeCarriers = $this->shipconfig->getActiveCarriers();

        foreach ($activeCarriers as $carrierCode => $carrierModel) {

            if ($carrierMethods = $carrierModel->getAllowedMethods()) {
                foreach ($carrierMethods as $methodCode => $method) {
                    /* Amasty Table Rates FIX */
                    if ($carrierCode == 'amstrates') {

                        $orderNewShippingMethodShort = str_replace('amstrates_', '', $orderNewShippingMethod);

                        if ($orderNewShippingMethodShort != (string)$methodCode) {
                            continue;
                        }
                    }
                    /* End Fix*/


                    if ((false !== strpos($orderNewShippingMethod, $carrierCode)) ||
                        (false !== strpos($orderNewShippingMethod, (string)$methodCode))) {
                        $methodTitle = $method;
                        break;
                    }
                }

                if ($methodTitle) {
                    $carrierTitle = $this->scopeConfig
                        ->getValue('carriers/' . $carrierCode . '/title');
                    $methodTitle = $carrierTitle . ' - ' . $methodTitle;

                    //Fix for amasty shipping rates,since we need use not method title but label instead
                    if ($this->isAmastyShippingMethodsRepresented()) {
                        $connection = $this->resourceConnection->getConnection();
                        $tableName = $this->resourceConnection->getTableName('amasty_table_method');
                        $amastyMethodName = trim($methodTitle, '- ');

                        $select = $connection->select()->from($tableName)
                            ->where('name = ?', $amastyMethodName)
                            ->where('aml.store_id = ?', (int)$this->order->getStoreId())
                            ->joinLeft(
                                ['aml' => $this->resourceConnection->getTableName(self::AMASTY_METHOD_LABEL_TABLE)],
                                $tableName .'.id = aml.method_id',
                                ['label' => 'aml.label']
                            );

                        $result = $connection->fetchRow($select);
                        $methodTitle = $result['label'] ?? $methodTitle;
                    }
                    break;
                }
            }
        }

        if (!$methodTitle) {
            return '';
        }

        return $methodTitle;
    }

    /**
     * @param string $customShippingPrice
     * @return void
     */
    private function reCollectTotalsWithCustomShippingPrice(string $customShippingPrice): void
    {
        $this->quote->getShippingAddress()->setShippingAmount((float)$customShippingPrice);

        $taxRateId = $this->quoteManager->_getQuote()->getData('mf_tax_rate_id');
        if (is_null($taxRateId)) {
            $taxRateId = $this->quoteManager->getTaxRateIdFromQuote();
        }
        $this->taxManager->addTaxRate((int)$taxRateId);

        $this->registry->register(
            'mf_order_edit_shipping_custom_price',
            $this->getPricesForShippingMethod('', $customShippingPrice)['price'] ?? null
        );
        $this->registry->register(
            'mf_order_edit_shipping_custom_base_price',
            $this->getPricesForShippingMethod('', $customShippingPrice)['basePrice'] ?? null
        );

        $this->quote->collectTotals();

        $this->registry->unregister('mf_order_edit_shipping_custom_price');
        $this->registry->unregister('mf_order_edit_shipping_custom_base_price');
    }
}
