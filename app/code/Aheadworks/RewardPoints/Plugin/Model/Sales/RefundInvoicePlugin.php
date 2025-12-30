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

namespace Aheadworks\RewardPoints\Plugin\Model\Sales;

use Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface;
use Magento\Sales\Api\RefundInvoiceInterface;

class RefundInvoicePlugin
{
    /**
     * @param CustomerRewardPointsManagementInterface $customerRewardPointsService
     */
    public function __construct(
        private readonly CustomerRewardPointsManagementInterface $customerRewardPointsService
    ) {
    }

    /**
     * Refund Reward Points to customer on credit memo
     *
     * @param RefundInvoiceInterface $subject
     * @param int $result
     * @return int
     */
    public function afterExecute(RefundInvoiceInterface $subject, int $result): int
    {
        $this->customerRewardPointsService->refundToRewardPoints($result);
        $this->customerRewardPointsService->reimbursedSpentRewardPoints($result);
        $this->customerRewardPointsService->cancelEarnedPointsRefundOrder($result);

        return $result;
    }
}
