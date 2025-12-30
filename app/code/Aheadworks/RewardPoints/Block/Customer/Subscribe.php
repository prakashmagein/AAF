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

namespace Aheadworks\RewardPoints\Block\Customer;

use Aheadworks\RewardPoints\Model\Config\Frontend\Label\Resolver as LabelResolver;
use Aheadworks\RewardPoints\Model\Source\SubscribeStatus;
use Aheadworks\RewardPoints\Block\Customer\RewardPointsBalance\Account\Transaction;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\RewardPoints\Model\Config;
use Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Aheadworks\RewardPoints\Model\Calculator\RateCalculator;

class Subscribe extends RewardPointsBalance
{
    /**
     * @var string
     */
    protected $_template = 'Aheadworks_RewardPoints::customer/newsletter/subscribe.phtml';

    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @param Context $context
     * @param Config $config
     * @param CustomerRewardPointsManagementInterface $customerRewardPointsService
     * @param CurrentCustomer $currentCustomer
     * @param PriceHelper $priceHelper
     * @param Transaction $transaction
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param RateCalculator $rateCalculator
     * @param LabelResolver $labelResolver
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        CustomerRewardPointsManagementInterface $customerRewardPointsService,
        CurrentCustomer $currentCustomer,
        PriceHelper $priceHelper,
        Transaction $transaction,
        \Magento\Framework\App\Http\Context $httpContext,
        RateCalculator $rateCalculator,
        LabelResolver $labelResolver,
        array $data = []
    ) {
        $this->transaction = $transaction;
        parent::__construct(
            $context,
            $config,
            $customerRewardPointsService,
            $currentCustomer,
            $priceHelper,
            $httpContext,
            $rateCalculator,
            $labelResolver,
            $data
        );
    }

    /**
     * Is balance update subscribed customer or not
     *
     * @return bool
     */
    public function isBalanceUpdateSubscribed()
    {
        $balanceUpdateNotificationStatus = $this->customerRewardPointsService
            ->getCustomerBalanceUpdateNotificationStatus($this->currentCustomer->getCustomerId());
        return $balanceUpdateNotificationStatus == SubscribeStatus::SUBSCRIBED ? true : false;
    }

    /**
     * Is expired reminders subscribed customer or not
     *
     * @return bool
     */
    public function isExpirationSubscribed()
    {
        $expirationNotificationStatus = $this->customerRewardPointsService
            ->getCustomerExpirationNotificationStatus($this->currentCustomer->getCustomerId());
        return $expirationNotificationStatus == SubscribeStatus::SUBSCRIBED ? true : false;
    }

    /**
     * Show block or not
     *
     * @return bool
     */
    public function canShow()
    {
        $customerRewardPointsSpendRate = $this->customerRewardPointsService
            ->isCustomerRewardPointsSpendRate($this->currentCustomer->getCustomerId());
        $customerRewardPointsSpendRateByGroup = $this->customerRewardPointsService
            ->isCustomerRewardPointsSpendRateByGroup($this->currentCustomer->getCustomerId());
        $customerRewardPointsEarnRate = $this->customerRewardPointsService
            ->isCustomerRewardPointsEarnRate($this->currentCustomer->getCustomerId());
        $customerRewardPointsEarnRateByGroup = $this->customerRewardPointsService
            ->isCustomerRewardPointsEarnRateByGroup($this->currentCustomer->getCustomerId());

        return $this->transaction->getTransactions() && count($this->transaction->getTransactions()->getItems())
            && $customerRewardPointsSpendRateByGroup && $customerRewardPointsSpendRate
            && $customerRewardPointsEarnRateByGroup && $customerRewardPointsEarnRate;
    }
}
