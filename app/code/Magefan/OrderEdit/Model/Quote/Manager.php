<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Model\Quote;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Manager
{
    /**
     * @var PriceCurrencyInterface
     */
    public $priceCurrency;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var RequestInterface
     */
    protected $request;

    protected $quoteItemsHaveDifferentTaxPercents;

    /**
     * @param PriceCurrencyInterface $priceCurrency
     * @param ObjectManagerInterface $objectManager
     * @param ResourceConnection $resourceConnection
     * @param RequestInterface $request
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        ObjectManagerInterface $objectManager,
        ResourceConnection $resourceConnection,
        RequestInterface $request
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->objectManager = $objectManager;
        $this->resourceConnection = $resourceConnection;
        $this->request = $request;
    }

    /**
     * @return int
     */
    public function getTaxRateIdFromQuote():int
    {
        $connection = $this->resourceConnection->getConnection();

        $select = $connection->select()
            ->from(
                ['sot' => $this->resourceConnection->getTableName('sales_order_tax')],
                ['code']
            )
            ->joinLeft(
                ['tcr' => $this->resourceConnection->getTableName('tax_calculation_rate')],
                'sot.code = tcr.code',
                ['tax_calculation_rate_id']
            )
            ->where(
                'sot.order_id = ?',
                (int)$this->request->getParam('order_id')
            );

        $data = $connection->fetchRow($select);

        return (int)($data['tax_calculation_rate_id'] ?? 0);
    }

    /**
     * @return mixed
     */
    public function _getQuote()
    {
        return $this->_getSession()->getQuote();
    }

    /**
     * @return mixed
     */
    public function _getSession()
    {
        return $this->objectManager->get(\Magento\Backend\Model\Session\Quote::class);
    }

    /**
     * @return bool
     */
    public function haveQuoteItemsDifferentTaxPercents(): bool
    {
        if (!isset($this->quoteItemsHaveDifferentTaxPercents)) {
            $taxPercents = [];

            foreach ($this->_getQuote()->getAllVisibleItems() as $quoteItem) {
                $taxPercents[] = (float)$quoteItem->getTaxPercent();
            }

            $this->quoteItemsHaveDifferentTaxPercents = (1 < count(array_unique($taxPercents)));
        }

        return $this->quoteItemsHaveDifferentTaxPercents;
    }
}
