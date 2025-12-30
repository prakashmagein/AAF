<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Block\Adminhtml\Order\Product;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\App\ResourceConnection;
use Magento\Backend\Block\Template;
use Magento\Framework\ObjectManagerInterface;
use Magefan\OrderEdit\Model\Quote\Manager as QuoteManager;
use Magento\Framework\App\ProductMetadataInterface;

class TaxRates extends Template
{
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var QuoteManager
     */
    protected $quoteManager;

    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @param Template\Context $context
     * @param ResourceConnection $resourceConnection
     * @param ObjectManagerInterface $objectManager
     * @param QuoteManager $quoteManager
     * @param array $data
     * @param JsonHelper|null $jsonHelper
     * @param DirectoryHelper|null $directoryHelper
     * @param ProductMetadataInterface|null $productMetadata
     */
    public function __construct(
        Template\Context $context,
        ResourceConnection $resourceConnection,
        ObjectManagerInterface $objectManager,
        QuoteManager $quoteManager,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null,
        ProductMetadataInterface $productMetadata = null
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->objectManager = $objectManager;
        $this->quoteManager = $quoteManager;
        $this->productMetadata = $productMetadata ?:  \Magento\Framework\App\ObjectManager::getInstance()
            ->get(ProductMetadataInterface::class);

        if (version_compare($this->productMetadata->getVersion(), '2.4.0', '<')) {
            parent::__construct($context, $data);
        } else {
            parent::__construct($context, $data, $jsonHelper, $directoryHelper);
        }
    }

    /**
     * @return array
     */
    public function getTaxRatesCodes(): array
    {
        $connection = $this->resourceConnection->getConnection();

        $select = $connection->select()
            ->from(
                ['ce' => $this->resourceConnection->getTableName('tax_calculation_rate')],
                ['tax_calculation_rate_id', 'code']
            );

        $data = $connection->fetchAll($select);
        $codes = [];

        foreach ($data as $item) {
            if (isset($item['tax_calculation_rate_id']) && isset($item['code'])) {
                $codes[$item['tax_calculation_rate_id']] = $item['code'];
            }
        }

        return $codes;
    }

    /**
     * @return int
     */
    public function getTaxRateIdFromQuote():int
    {
        $mfTaxRateId = $this->getSession()->getQuote()->getData('mf_tax_rate_id');

        if (is_null($mfTaxRateId)) {
            $mfTaxRateId = $this->quoteManager->getTaxRateIdFromQuote();
        }

        return (int)$mfTaxRateId;
    }

    /**
     * @return mixed
     */
    protected function getSession()
    {
        return $this->objectManager->get(\Magento\Backend\Model\Session\Quote::class);
    }
}
