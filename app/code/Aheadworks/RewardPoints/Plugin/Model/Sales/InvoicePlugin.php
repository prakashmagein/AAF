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
namespace Aheadworks\RewardPoints\Plugin\Model\Sales;

use Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface;
use Magento\Sales\Model\Order\Invoice;

/**
 * Class Aheadworks\RewardPoints\Plugin\Model\Sales\InvoicePlugin
 */
class InvoicePlugin
{
    /**
     * @var CustomerRewardPointsManagementInterface
     */
    private $customerRewardPointsService;

    /**
     * @var bool
     */
    private $isInvoicePaid = false;

    /**
     * @param CustomerRewardPointsManagementInterface $customerRewardPointsService
     */
    public function __construct(
        CustomerRewardPointsManagementInterface $customerRewardPointsService
    ) {
        $this->customerRewardPointsService = $customerRewardPointsService;
    }

    /**
     * Setting paid flag to invoice for adding points
     *
     * @param Invoice $invoice
     * @return Invoice
     */
    public function afterPay(Invoice $invoice)
    {
        $this->isInvoicePaid = true;
        return $invoice;
    }

    /**
     * Add earned points to customer after save invoice
     *
     * @param Invoice $subject
     * @return Invoice
     */
    public function afterSave(Invoice $subject)
    {
        if ($this->isInvoicePaid) {
            $this->customerRewardPointsService->addPointsForPurchases($subject->getEntityId());
            $this->isInvoicePaid = false;
        }
        return $subject;
    }
}
