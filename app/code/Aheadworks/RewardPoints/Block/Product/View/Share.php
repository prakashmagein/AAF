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

namespace Aheadworks\RewardPoints\Block\Product\View;

use Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface;
use Aheadworks\RewardPoints\Model\Calculator\RateCalculator;
use Aheadworks\RewardPoints\Model\Config;
use Aheadworks\RewardPoints\Model\Config\Frontend\Label\Resolver as LabelResolver;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Http\Context as HttpContext;

class Share extends Template
{
    /**
     * Template of Twitter sharing link
     */
    public const TWITTER_SHARE_LINK = 'https://twitter.com/intent/tweet?url=';

    /**
     * Block template filename
     *
     * @var string
     */
    protected $_template = 'Aheadworks_RewardPoints::product/view/share.phtml';

    /**
     * @param Context $context
     * @param CustomerRewardPointsManagementInterface $customerRewardPointsService
     * @param RateCalculator $rateCalculator
     * @param Config $config
     * @param Session $customerSession
     * @param PriceHelper $priceHelper
     * @param HttpContext $httpContext
     * @param ProductRepositoryInterface $productRepository
     * @param LabelResolver $labelResolver
     * @param array $data
     */
    public function __construct(
        private readonly Context $context,
        private readonly CustomerRewardPointsManagementInterface $customerRewardPointsService,
        private readonly RateCalculator $rateCalculator,
        private readonly Config $config,
        private readonly Session $customerSession,
        private readonly PriceHelper $priceHelper,
        private readonly HttpContext $httpContext,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly LabelResolver $labelResolver,
        array $data = []
    ) {
        parent::__construct($this->context, $data);
    }

    /**
     * Is ajax request or not
     *
     * @return bool
     */
    public function isAjax()
    {
        return $this->_request->isAjax();
    }

    /**
     * Retrieve product
     *
     * @return \Magento\Catalog\Model\Product
     */
    private function getProduct()
    {
        $productId = $this->getRequest()->getParam('product_id', null)
            ? $this->getRequest()->getParam('product_id')
            : $this->getRequest()->getParam('id');

        return $this->productRepository->getById($productId);
    }

    /**
     * Retrieve config value for Display social sharing buttons at product page
     *
     * @return boolean
     */
    public function isDisplayBlock()
    {
        $customerRewardPointsEarnRate = $this->customerRewardPointsService
            ->isCustomerRewardPointsEarnRate($this->customerSession->getId());
        $customerRewardPointsEarnRateByGroup = $this->customerRewardPointsService
            ->isCustomerRewardPointsEarnRateByGroup($this->customerSession->getId());

        return $this->config->isDisplayShareLinks()
            && $customerRewardPointsEarnRateByGroup && $customerRewardPointsEarnRate;
    }

    /**
     * Get action url
     *
     * @return string
     */
    public function getAjaxActionUrl()
    {
        return $this->getUrl('aw_rewardpoints/share');
    }

    /**
     * Is customer is login
     *
     * @return boolean
     */
    public function isGuest()
    {
        return !(bool)$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    /**
     * Get product id
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->getProduct()->getId();
    }

    /**
     * Get current product url
     *
     * @return string
     */
    public function getCurrentProductUrl()
    {
        return $this->getProduct()->getUrlModel()->getUrl($this->getProduct());
    }

    /**
     * Get customer awarded points for share
     *
     * @return float
     */
    public function getAwardedPointsForShare()
    {
        return $this->config->getAwardedPointsForShare();
    }

    /**
     * Get customer awarded amount
     *
     * @return float
     */
    private function getAwardedAmount()
    {
        $points = $this->getAwardedPointsForShare();
        if ($points > 0) {
            return $this->rateCalculator->calculateRewardDiscount($this->customerSession->getId(), $points);
        }

        return 0;
    }

    /**
     * Get formatted customer awarded amount
     *
     * @return string
     */
    public function getFormattedAwardedAmount()
    {
        return $this->priceHelper->currency($this->getAwardedAmount(), true, false);
    }

    /**
     * Get twitter share url
     *
     * @return string
     */
    public function getTwitterShareUrl()
    {
        return self::TWITTER_SHARE_LINK . $this->escapeUrl($this->getCurrentProductUrl());
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
