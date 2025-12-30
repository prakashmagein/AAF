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
namespace Aheadworks\RewardPoints\Plugin\Model\Service;

use Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface;
use Magento\Sales\Model\Service\CreditmemoService;
use Magento\Sales\Api\Data\CreditmemoInterface;

/**
 * Class CreditmemoServicePlugin
 *
 * @package Aheadworks\RewardPoints\Plugin\Model\Service
 */
class CreditmemoServicePlugin
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
     * @param  CreditmemoService $subject
     * @param  CreditmemoInterface $result
     * @return CreditmemoInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterRefund(CreditmemoService $subject, CreditmemoInterface $result)
    {
        $this->customerRewardPointsService->refundToRewardPoints($result->getEntityId());
        $this->customerRewardPointsService->reimbursedSpentRewardPoints($result->getEntityId());
        $this->customerRewardPointsService->cancelEarnedPointsRefundOrder($result->getEntityId());

        return $result;
    }
}
