<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Plugin\Backend\Magento\Tax\Model;

use Magento\Framework\Registry;
use Magento\Tax\Model\Calculation as Subject;
use Magefan\OrderEdit\Model\Quote\Manager as QuoteManager;

class Calculation
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
     * @param $result
     * @return mixed
     */
    public function afterGetRateRequest(Subject $subject, $result)
    {
        $customerTaxClassId = $this->registry->registry('mf_order_edit_customer_tax_class_id');

        if (!$this->quoteManager->haveQuoteItemsDifferentTaxPercents()
            && !is_null($customerTaxClassId)) {
            $taxCountryId = $this->registry->registry('mf_order_edit_tax_country_id');
            $result->setCustomerClassId($customerTaxClassId);
            $result->setCountryId($taxCountryId);
        }

        return $result;
    }
}
