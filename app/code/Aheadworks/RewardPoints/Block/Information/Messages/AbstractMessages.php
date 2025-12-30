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

namespace Aheadworks\RewardPoints\Block\Information\Messages;

use Aheadworks\RewardPoints\Model\Config;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Aheadworks\RewardPoints\Model\Service\PointsSummaryService;
use Aheadworks\RewardPoints\Model\Calculator\RateCalculator;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Checkout\Model\Session as CheckoutSession;
use Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface;
use Magento\Newsletter\Model\Subscriber;
use Magento\Quote\Model\Cart\CartTotalRepository;
use Aheadworks\RewardPoints\Model\Calculator\Earning as EarningCalculator;
use Magento\Framework\View\Element\Template;

abstract class AbstractMessages extends Template
{

    /**
     * @param Context $context
     * @param Config $config
     * @param CurrentCustomer $currentCustomer
     * @param HttpContext $httpContext
     * @param PointsSummaryService $pointsSummaryService
     * @param RateCalculator $rateCalculator
     * @param PriceHelper $priceHelper
     * @param CheckoutSession $checkoutSession
     * @param CustomerRewardPointsManagementInterface $customerRewardPointsService
     * @param CartTotalRepository $cartTotalRepository
     * @param EarningCalculator $earningCalculator
     * @param Subscriber $subscriber
     * @param array $data
     */
    public function __construct(
        protected readonly Context $context,
        protected readonly Config $config,
        private readonly CurrentCustomer $currentCustomer,
        private readonly HttpContext $httpContext,
        protected readonly PointsSummaryService $pointsSummaryService,
        protected readonly RateCalculator $rateCalculator,
        private readonly PriceHelper $priceHelper,
        protected readonly CheckoutSession $checkoutSession,
        protected readonly CustomerRewardPointsManagementInterface $customerRewardPointsService,
        protected readonly CartTotalRepository $cartTotalRepository,
        protected readonly EarningCalculator $earningCalculator,
        protected readonly Subscriber $subscriber,
        array $data = []
    ) {
        parent::__construct($this->context, $data);
    }

    /**
     * Can show block or not
     *
     * @return bool
     */
    abstract public function canShow();

    /**
     * Retrieve block message
     *
     * @return string
     */
    abstract public function getMessage();

    /**
     * {@inheritdoc}
     */
    public function toHtml()
    {
        if (!$this->getTemplate()) {
            return $this->getMessage();
        }
        return parent::toHtml();
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
     * Checking customer login status
     *
     * @return bool
     */
    public function isCustomerLoggedIn()
    {
        return (bool)$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    /**
     * Retrieve potential earn money value
     *
     * @param int $points
     * @return float
     */
    public function getEarnMoneyByPoints($points)
    {
        $money = $this->rateCalculator->calculateRewardDiscount(
            $this->getCustomerId(),
            $points
        );
        return $money
            ? ' (' . $this->priceHelper->currency($money) . ')'
            : '';
    }

    /**
     * Retrieve customer id
     *
     * @return int
     */
    protected function getCustomerId()
    {
        return $this->currentCustomer->getCustomerId();
    }

    /**
     * Get label name reward points
     *
     * @return string
     * @throws LocalizedException
     */
    protected function getLabelNameRewardPoints(): string
    {
        $websiteId = (int)$this->context->getStoreManager()->getWebsite()->getId();
        $label = $this->config->getLabelNameRewardPoints($websiteId);

        return $label !== Config::DEFAULT_LABEL_NAME ? $label : Config::DEFAULT_POINTS_NAME;
    }
}
