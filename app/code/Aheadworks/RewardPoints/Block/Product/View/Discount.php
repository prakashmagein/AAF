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
use Aheadworks\RewardPoints\Model\CategoryAllowed;
use Aheadworks\RewardPoints\Model\Config\Frontend\Label\Resolver as LabelResolver;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\RewardPoints\Model\Config;
use Magento\Customer\Model\Session;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Aheadworks\RewardPoints\Model\Validator\Product\Price\Discount\Checker;
use Magento\Framework\View\Element\Template;

class Discount extends Template
{
    /**
     * Block template filename
     *
     * @var string
     */
    protected $_template = 'Aheadworks_RewardPoints::product/view/discount.phtml';

    /**
     * @param Context $context
     * @param CustomerRewardPointsManagementInterface $customerRewardPointsService
     * @param Session $customerSession
     * @param Config $config
     * @param RateCalculator $rateCalculator
     * @param PriceHelper $priceHelper
     * @param CategoryAllowed $categoryAllowed
     * @param ProductRepositoryInterface $productRepository
     * @param Checker $discountChecker
     * @param LabelResolver $labelResolver
     * @param array $data
     */
    public function __construct(
        private readonly Context $context,
        private readonly CustomerRewardPointsManagementInterface $customerRewardPointsService,
        private readonly Session $customerSession,
        private readonly Config $config,
        private readonly RateCalculator $rateCalculator,
        private readonly PriceHelper $priceHelper,
        private readonly CategoryAllowed $categoryAllowed,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly Checker $discountChecker,
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
     * Retrieve config value for Display block with discount information
     *
     * @return boolean
     */
    public function isDisplayBlock()
    {
        $customerRewardPointsOnceMinBalance = $this->customerRewardPointsService
            ->getCustomerRewardPointsOnceMinBalance($this->customerSession->getId());
        $customerRewardPointsSpendRate = $this->customerRewardPointsService
            ->isCustomerRewardPointsSpendRate($this->customerSession->getId());
        $customerRewardPointsSpendRateByGroup = $this->customerRewardPointsService
            ->isCustomerRewardPointsSpendRateByGroup($this->customerSession->getId());
        $product = $this->productRepository->getById($this->getRequest()->getParam('id'));

        return $this->config->isDisplayDiscountInfoBlock() && $this->isAllowedCategoriesForSpend()
            && $customerRewardPointsOnceMinBalance == 0
            && $customerRewardPointsSpendRateByGroup && $customerRewardPointsSpendRate
            && !$this->discountChecker->validateProductForSpend($product);
    }

    /**
     * Get customer available points
     *
     * @return int
     */
    public function getAvailablePoints()
    {
        if ($this->customerSession->getId()) {
            return $this->customerRewardPointsService->getCustomerRewardPointsBalance($this->customerSession->getId());
        }

        return 0;
    }

    /**
     * Get customer available amount
     *
     * @return float
     */
    private function getAvailableAmount()
    {
        $points = $this->getAvailablePoints();
        if ($points > 0) {
            return $this->rateCalculator->calculateRewardDiscount($this->customerSession->getId(), $points);
        }

        return 0;
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

    /**
     * Get formatted customer available amount
     *
     * @return string
     */
    public function getFormattedAvailableAmount()
    {
        return $this->priceHelper->currency($this->getAvailableAmount(), true, false);
    }

    /**
     * Is allowed category products for spend
     *
     * @return boolean
     */
    private function isAllowedCategoriesForSpend()
    {
        return $this->categoryAllowed->isAllowedCategoryForSpendPoints($this->getProduct()->getCategoryIds());
    }

    /**
     * Retrieve current product
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getProduct()
    {
        $productId = $this->getRequest()->getParam('product_id', null)
            ? $this->getRequest()->getParam('product_id')
            : $this->getRequest()->getParam('id');
        return $this->productRepository->getById($productId);
    }
}
