<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Plugin\Backend\Magento\Tax\Api;

use Magento\Framework\Registry;
use Magento\Tax\Api\TaxClassManagementInterface as Subject;
use Magefan\OrderEdit\Model\Quote\Manager as QuoteTaxManager;

class TaxClassManagementInterface
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var QuoteTaxManager
     */
    protected $quoteTaxManager;

    /**
     * @param Registry $registry
     * @param QuoteTaxManager $quoteTaxManager
     */
    public function __construct(
        Registry $registry,
        QuoteTaxManager $quoteTaxManager
    ) {
        $this->registry = $registry;
        $this->quoteTaxManager = $quoteTaxManager;
    }

    /**
     * @param Subject $subject
     * @param $result
     * @return mixed
     */
    public function afterGetTaxClassId(Subject $subject, $result)
    {
        if (!$this->quoteTaxManager->haveQuoteItemsDifferentTaxPercents()
            && $productTaxClassId = (string)$this->registry->registry('mf_order_edit_product_tax_class_id')) {
            return $productTaxClassId;
        }

        return $result;
    }
}
