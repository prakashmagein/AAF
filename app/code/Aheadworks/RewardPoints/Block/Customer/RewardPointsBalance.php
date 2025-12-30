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

use Aheadworks\RewardPoints\Model\Calculator\RateCalculator;
use Aheadworks\RewardPoints\Model\Config;
use Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface;
use Aheadworks\RewardPoints\Model\Config\Frontend\Label\Resolver as LabelResolver;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Http\Context as HttpContext;

class RewardPointsBalance extends Template
{
    /**
     * @param Context $context
     * @param Config $config
     * @param CustomerRewardPointsManagementInterface $customerRewardPointsService
     * @param CurrentCustomer $currentCustomer
     * @param PriceHelper $priceHelper
     * @param HttpContext $httpContext
     * @param RateCalculator $rateCalculator
     * @param LabelResolver $labelResolver
     * @param array $data
     */
    public function __construct(
        protected Context $context,
        protected readonly Config $config,
        protected readonly CustomerRewardPointsManagementInterface $customerRewardPointsService,
        protected readonly CurrentCustomer $currentCustomer,
        protected readonly PriceHelper $priceHelper,
        protected readonly HttpContext $httpContext,
        protected readonly RateCalculator $rateCalculator,
        protected readonly LabelResolver $labelResolver,
        array $data = []
    ) {
        parent::__construct($this->context, $data);
    }

    /**
     * Get customer balance in points
     *
     * @return int
     */
    public function getCustomerRewardPointsBalance()
    {
        return (int)$this->customerRewardPointsService->getCustomerRewardPointsBalance(
            $this->currentCustomer->getCustomerId()
        );
    }

    /**
     * Get label name reward points
     *
     * @return string
     * @throws LocalizedException
     */
    public function getLabelNameRewardPoints(): string
    {
        return $this->config->getLabelNameRewardPoints($this->getWebsiteId());
    }

    /**
     * Get tab label name reward points
     *
     * @return string
     * @throws LocalizedException
     */
    public function getTabLabelNameRewardPoints(): string
    {
        return $this->config->getTabLabelNameRewardPoints($this->getWebsiteId());
    }

    /**
     * Get formatted customer balance currency
     *
     * @return string
     */
    public function getFormattedCustomerBalanceCurrency()
    {
        return $this->priceHelper->currency(
            $this->getCustomerRewardPointsBalanceBaseCurrency(),
            true,
            false
        );
    }

    /**
     * Get frontend explainer page link
     *
     * @return string
     */
    public function getFrontendExplainerPageLink()
    {
        return $this->getUrl($this->config->getFrontendExplainerPage());
    }

    /**
     * Retrieve customer reward points balance in base currency
     *
     * @return float
     */
    protected function getCustomerRewardPointsBalanceBaseCurrency()
    {
        return $this->customerRewardPointsService->getCustomerRewardPointsBalanceBaseCurrency(
            $this->currentCustomer->getCustomerId()
        );
    }

    /**
     * Get website id
     *
     * @return int
     * @throws LocalizedException
     */
    public function getWebsiteId(): int
    {
       return (int)$this->context->getStoreManager()->getWebsite()->getId();
    }
}
