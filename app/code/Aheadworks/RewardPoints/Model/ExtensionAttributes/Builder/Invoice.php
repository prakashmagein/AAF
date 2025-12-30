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

namespace Aheadworks\RewardPoints\Model\ExtensionAttributes\Builder;

use Magento\Sales\Api\Data\InvoiceExtension;
use Magento\Sales\Api\Data\InvoiceExtensionFactory;
use Magento\Sales\Api\Data\InvoiceItemExtension;
use Magento\Sales\Api\Data\InvoiceItemExtensionFactory;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\InvoiceItemInterface;

/**
 * Class Invoice
 */
class Invoice
{
    /**
     * @var InvoiceExtensionFactory
     */
    private $invoiceExtensionFactory;

    /**
     * @var InvoiceItemExtensionFactory
     */
    private $invoiceItemExtensionFactory;

    /**
     * @param InvoiceExtensionFactory $invoiceExtensionFactory
     * @param InvoiceItemExtensionFactory $invoiceItemExtensionFactory
     */
    public function __construct(
        InvoiceExtensionFactory $invoiceExtensionFactory,
        InvoiceItemExtensionFactory $invoiceItemExtensionFactory
    ) {
        $this->invoiceExtensionFactory = $invoiceExtensionFactory;
        $this->invoiceItemExtensionFactory = $invoiceItemExtensionFactory;
    }

    /**
     * Attach Reward Points attributes
     *
     * @param InvoiceInterface $invoice
     * @return InvoiceInterface
     */
    public function attachAttributes(InvoiceInterface $invoice): InvoiceInterface
    {
        /** @var InvoiceExtension $invoiceExtension */
        $invoiceExtension = $invoice->getExtensionAttributes()
            ?: $this->invoiceExtensionFactory->create();

        $invoiceExtension
            ->setAwUseRewardPoints($invoice->getAwUseRewardPoints())
            ->setAwRewardPointsAmount($invoice->getAwRewardPointsAmount())
            ->setBaseAwRewardPointsAmount($invoice->getBaseAwRewardPointsAmount())
            ->setAwRewardPoints($invoice->getAwRewardPoints())
            ->setAwRewardPointsDescription($invoice->getAwRewardPointsDescription());

        $invoice->setExtensionAttributes($invoiceExtension);

        $items = $invoice->getItems() ?: [];
        foreach ($items as $item) {
            $this->attachAttributesToItem($item);
        }

        return $invoice;
    }

    /**
     * Attach Reward Points attributes to item
     *
     * @param InvoiceItemInterface $item
     * @return InvoiceItemInterface
     */
    public function attachAttributesToItem(InvoiceItemInterface $item): InvoiceItemInterface
    {
        /** @var InvoiceItemExtension $invoiceItemExtension */
        $invoiceItemExtension = $item->getExtensionAttributes()
            ?: $this->invoiceItemExtensionFactory->create();

        $invoiceItemExtension
            ->setAwRewardPointsAmount($item->getAwRewardPointsAmount())
            ->setBaseAwRewardPointsAmount($item->getBaseAwRewardPointsAmount())
            ->setAwRewardPoints($item->getAwRewardPoints());

        $item->setExtensionAttributes($invoiceItemExtension);

        return $item;
    }
}
