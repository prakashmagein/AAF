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

namespace Aheadworks\RewardPoints\Block\Customer\RewardPointsBalance;

use Aheadworks\RewardPoints\Block\Customer\RewardPointsBalance;
use Magento\Framework\Exception\LocalizedException;

class Account extends RewardPointsBalance
{
    /**
     * @var []
     */
    private $minRateIsNeeded;

    /**
     * Retrieve min balance for use at checkout
     *
     * @return int
     */
    public function getOnceMinBalance()
    {
        return $this->customerRewardPointsService->getCustomerRewardPointsOnceMinBalance(
            $this->currentCustomer->getCustomerId()
        );
    }

    /**
     * Is customer spend rate
     *
     * @return bool
     */
    public function isCustomerRewardPointsSpendRate()
    {
        return $this->customerRewardPointsService->isCustomerRewardPointsSpendRate(
            $this->currentCustomer->getCustomerId()
        );
    }

    /**
     * Retrieve formatted spend customer is needed
     *
     * @return string
     */
    public function getFormattedSpendCustomerIsNeeded()
    {
        return $this->priceHelper->currency(
            $this->getLifetimeSalesDifference(),
            true,
            false
        );
    }

    /**
     * Retrieve customer will earn points
     *
     * @return int
     */
    public function getCustomerWillEarnPoints()
    {
        return $this->rateCalculator->calculateEarnPoints(
            $this->currentCustomer->getCustomerId(),
            $this->getLifetimeSalesDifference()
        );
    }

    /**
     * Retrieve formatted customer bonus discount
     *
     * @return string
     */
    public function getFormattedCustomerBonusDiscount()
    {
        $customerSpendRate = $this->getMinRateIsNeeded();
        $discount = $this->rateCalculator->calculateRewardDiscount(
            $this->currentCustomer->getCustomerId(),
            $this->getCustomerRewardPointsBalance() + $this->getCustomerWillEarnPoints(),
            null,
            $customerSpendRate['spend_rate']
        );

        return $this->priceHelper->currency(
            $discount,
            true,
            false
        );
    }

    /**
     * Retrieve customer difference lifetime sales
     *
     * @return float
     */
    private function getLifetimeSalesDifference()
    {
        $customerSpendRate = $this->getMinRateIsNeeded();
        $spendRateSalesAmount = (float)$customerSpendRate['spend_rate']->getLifetimeSalesAmount();
        $customerRateSalesAmount = (float)$customerSpendRate['lifetime_sales'];
        return $spendRateSalesAmount > $customerRateSalesAmount ? $spendRateSalesAmount - $customerRateSalesAmount : 0;
    }

    /**
     * Retrieve min rate is needed to customer
     *
     * @return []
     */
    private function getMinRateIsNeeded()
    {
        if (null == $this->minRateIsNeeded) {
            $this->minRateIsNeeded = $this->rateCalculator->getMinRateIsNeeded(
                $this->currentCustomer->getCustomerId()
            );
        }
        return $this->minRateIsNeeded;
    }

    /**
     * Get label name reward points
     *
     * @return string
     * @throws LocalizedException
     */
    public function getLabelNameRewardPoints(): string
    {
        $websiteId = (int)$this->context->getStoreManager()->getWebsite()->getId();

        return $this->labelResolver->getLabelNameRewardPoints($websiteId);
    }
}
