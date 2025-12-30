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
namespace Aheadworks\RewardPoints\Plugin\Model;

use Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface;
use Magento\Sales\Api\RefundOrderInterface;

/**
 * Class RefundOrderPlugin
 *
 * @package Aheadworks\RewardPoints\Plugin\Model
 */
class RefundOrderPlugin
{
    /**
     * @var CustomerRewardPointsManagementInterface
     */
    private $customerRewardPointsService;

    /**
     * @param CustomerRewardPointsManagementInterface $customerRewardPointsService
     */
    public function __construct(
        CustomerRewardPointsManagementInterface $customerRewardPointsService
    ) {
        $this->customerRewardPointsService = $customerRewardPointsService;
    }

    /**
     * Refund Reward Points to customer on credit memo
     *
     * @param RefundOrderInterface $subject
     * @param int $result
     * @return int
     * @since 100.1.3
     */
    public function afterExecute(RefundOrderInterface $subject, $result)
    {
        $this->customerRewardPointsService->refundToRewardPoints($result);
        $this->customerRewardPointsService->reimbursedSpentRewardPoints($result);
        $this->customerRewardPointsService->cancelEarnedPointsRefundOrder($result);

        return $result;
    }
}
