<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://aheadworks.com/end-user-license-agreement/
 *
 * @package    RewardPoints
 * @version    2.4.0
 * @copyright  Copyright (c) 2024 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\RewardPoints\Plugin\Model\Order;

use Aheadworks\RewardPoints\Model\ExtensionAttributes\Builder\Invoice as InvoiceExtensionAttributesBuilder;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\Data\InvoiceSearchResultInterface;

/**
 * Class InvoiceRepositoryPlugin
 */
class InvoiceRepositoryPlugin
{
    /**
     * @var InvoiceExtensionAttributesBuilder
     */
    private $invoiceExtensionAttributesBuilder;

    /**
     * @param InvoiceExtensionAttributesBuilder $invoiceExtensionAttributesBuilder
     */
    public function __construct(
        InvoiceExtensionAttributesBuilder $invoiceExtensionAttributesBuilder
    ) {
        $this->invoiceExtensionAttributesBuilder = $invoiceExtensionAttributesBuilder;
    }

    /**
     * Add data to invoice object
     *
     * @param InvoiceRepositoryInterface $subject
     * @param InvoiceSearchResultInterface $result
     * @return InvoiceSearchResultInterface
     */
    public function afterGetList(InvoiceRepositoryInterface $subject, InvoiceSearchResultInterface $result): InvoiceSearchResultInterface
    {
        foreach ($result->getItems() as $invoice) {
            $this->invoiceExtensionAttributesBuilder->attachAttributes($invoice);
        }

        return $result;
    }

    /**
     * Add data to invoice object
     *
     * @param InvoiceRepositoryInterface $subject
     * @param InvoiceInterface $invoice
     * @return InvoiceInterface
     */
    public function afterGet(InvoiceRepositoryInterface $subject, InvoiceInterface $invoice): InvoiceInterface
    {
        $this->invoiceExtensionAttributesBuilder->attachAttributes($invoice);

        return $invoice;
    }
}
