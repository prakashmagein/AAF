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
namespace Aheadworks\RewardPoints\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class ProcessOrderCreate
 *
 * @package Aheadworks\RewardPoints\Observer
 */
class ProcessOrderCreate implements ObserverInterface
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
     * Apply reward points for admin checkout
     *
     * @param Observer $observer
     * @return $this
     * @throws NoSuchEntityException No Reward Points to be used
     */
    public function execute(Observer $observer)
    {
        $quote = $observer->getEvent()->getOrderCreateModel()->getQuote();
        $request = $observer->getEvent()->getRequest();

        if (isset($request['payment']) && isset($request['payment']['aw_use_reward_points'])) {
            $awUseRewardPoints = (bool)$request['payment']['aw_use_reward_points'];
            if ($awUseRewardPoints && (!$quote->getCustomerId()
                    || !$this->customerRewardPointsService->getCustomerRewardPointsBalance($quote->getCustomerId()))
            ) {
                throw new NoSuchEntityException(__('No Reward Points to be used'));
            }

            $quote->setAwUseRewardPoints($awUseRewardPoints);
        }

        return $this;
    }
}
