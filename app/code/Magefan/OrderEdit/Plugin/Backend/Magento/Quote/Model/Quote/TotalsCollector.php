<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Plugin\Backend\Magento\Quote\Model\Quote;

use Magento\Framework\Registry;
use Magento\Quote\Model\Quote\TotalsCollector as Subject;
use Magefan\OrderEdit\Model\Quote\Manager as QuoteManager;

class TotalsCollector
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var QuoteManager
     */
    protected $quoteManager;

    /**
     * @param Registry $registry
     * @param QuoteManager $quoteManager
     */
    public function __construct(
        Registry $registry,
        QuoteManager $quoteManager
    ) {
        $this->registry = $registry;
        $this->quoteManager = $quoteManager;
    }

    /**
     * @param Subject $subject
     * @param $quote
     * @return array
     */
    public function beforeCollect(Subject $subject, $quote)
    {
        if (!$this->quoteManager->haveQuoteItemsDifferentTaxPercents()) {
            $this->setCustomTaxRegionIdToAllQuoteAddresses($quote);
        }

        return [$quote];
    }

    /**
     * @param $quote
     * @return void
     */
    private function setCustomTaxRegionIdToAllQuoteAddresses($quote): void
    {
        $taxRegionId = (string)$this->registry->registry('mf_order_edit_tax_region_id');

        if ($taxRegionId) {
            foreach ($quote->getAllAddresses() as $address) {
                $address->setRegionId($taxRegionId);
            }
        }
    }
}
