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

namespace Aheadworks\RewardPoints\Model\Service\RewardPointsCartService\SpendingData;

use Aheadworks\RewardPoints\Model\Calculator\Quote\Item as QuoteItemCalculator;
use Aheadworks\RewardPoints\Model\Calculator\RateCalculator;
use Aheadworks\RewardPoints\Model\Calculator\ShareCoveredCalculator;
use Aheadworks\RewardPoints\Model\Calculator\Spending\Checker as SpendingChecker;
use Aheadworks\RewardPoints\Model\Calculator\Spending\SpendItemManager;
use Aheadworks\RewardPoints\Model\Calculator\SpendRuleCalcManager;
use Aheadworks\RewardPoints\Model\Config;
use Aheadworks\RewardPoints\Model\Service\RewardPointsCartService\SpendingData;
use Aheadworks\RewardPoints\Model\Service\RewardPointsCartService\SpendingDataFactory;
use Aheadworks\RewardPoints\Model\Calculator\Validator as CartRuleValidator;
use Magento\Tax\Model\Config as TaxConfig;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Item\AbstractItem as QuoteAbstractItem;
use Aheadworks\RewardPoints\Model\Calculator\Spending\SpendItemInterfaceFactory;
use Aheadworks\RewardPoints\Model\SpendRule\CartSpendRule;
use Aheadworks\RewardPoints\Api\SpendRuleManagementInterface;

/**
 * Class Provider
 */
class Provider
{
    /**
     * Delta value for operations with float values
     */
    private const DELTA = 0.0001;

    /**
     * @param SpendingDataFactory $dataFactory
     * @param SpendingChecker $spendingChecker
     * @param QuoteItemCalculator $quoteItemCalculator
     * @param Config $config
     * @param RateCalculator $rateCalculator
     * @param ShareCoveredCalculator $shareCoveredCalculator
     * @param TaxConfig $taxConfig
     * @param CartRuleValidator $cartRuleValidator
     * @param SpendRuleCalcManager $spendRuleCalcManager
     * @param SpendItemInterfaceFactory $spendItemFactory
     * @param CartSpendRule $cartSpendRule
     * @param SpendItemManager $spendItemManager
     * @param SpendRuleManagementInterface $spendRuleManagement
     */
    public function __construct(
        private readonly SpendingDataFactory $dataFactory,
        private readonly SpendingChecker $spendingChecker,
        private readonly QuoteItemCalculator $quoteItemCalculator,
        private readonly Config $config,
        private readonly RateCalculator $rateCalculator,
        private readonly ShareCoveredCalculator $shareCoveredCalculator,
        private readonly TaxConfig $taxConfig,
        private readonly CartRuleValidator $cartRuleValidator,
        private readonly SpendRuleCalcManager $spendRuleCalcManager,
        private readonly SpendItemInterfaceFactory $spendItemFactory,
        private readonly CartSpendRule $cartSpendRule,
        private readonly SpendItemManager $spendItemManager,
        private readonly SpendRuleManagementInterface $spendRuleManagement
    ) {
    }

    /**
     * Retrieve calculated reward points data for applying on the specific quote
     *
     * @param Quote $quote
     * @param null $pointsQtyToApply
     * @return SpendingData
     * @throws \Zend_Db_Select_Exception
     */
    public function getDataByQuote($quote, $pointsQtyToApply = null)
    {
        $shippingAddress = $quote->getShippingAddress();
        $billingAddress = $quote->getBillingAddress();
        $quoteItems = $shippingAddress->getAllItems();
        if (empty($quoteItems)) {
            $quoteItems = $billingAddress->getAllItems();
        }
        return $this->getData(
            $quote,
            $quoteItems,
            $shippingAddress,
            $pointsQtyToApply
        );
    }

    /**
     * Retrieve calculated reward points data for applying on the specific quote
     *
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param int|null $pointsQtyToApply
     * @return SpendingData
     */
    public function getDataByShippingAssignment($quote, $shippingAssignment, $pointsQtyToApply = null)
    {
        $address = $shippingAssignment->getShipping()->getAddress();
        $items = $shippingAssignment->getItems();
        return $this->getData(
            $quote,
            $items,
            $address,
            $pointsQtyToApply
        );
    }

    /**
     * Retrieve calculated reward points data for applying
     *
     * @param Quote $quote
     * @param CartItemInterface[]|QuoteAbstractItem[] $quoteItemList
     * @param AddressInterface|Address $quoteAddress
     * @param null $pointsQtyToApply
     * @return SpendingData
     * @throws \Zend_Db_Select_Exception
     */
    private function getData(
        $quote,
        $quoteItemList,
        $quoteAddress,
        $pointsQtyToApply = null
    ): SpendingData {
        /** @var SpendingData $spendingData */
        $spendingData = $this->dataFactory->create();

        $customerId = (int) $quote->getCustomerId();
        $websiteId = (int) $quote->getStore()->getWebsiteId();

        $spendItems = [];

        if (!is_array($quoteItemList) || empty($quoteItemList)) {
            return $spendingData;
        }

        if ($this->config->areRestrictSpendingPointsWithCartPriceRules($websiteId)
            && $this->cartRuleValidator->canApplySalesRules($quote)) {
            return $spendingData;
        }

        foreach ($quoteItemList as $quoteItem) {
            if ($quoteItem->getParentItem()) {
                continue;
            }

            if ($this->spendingChecker->canSpendRewardPointsOnQuoteItem($quoteItem)) {
                $baseItemAmount = $this->quoteItemCalculator->calculateItemTotal($quoteItem);
                $compensationTaxItemAmount = (float)$quoteItem->getBaseDiscountTaxCompensationAmount();
                $baseTaxItemAmount = (float)$quoteItem->getBaseTaxAmount();

                if ($this->spendingChecker->canSpendRewardPointsPartlyOnChildren($quoteItem)) {
                    foreach ($quoteItem->getChildren() as $child) {
                        if (!$this->spendingChecker->canSpendRewardPointsOnQuoteItem($child)) {
                            $itemTotal = $this->quoteItemCalculator->calculateItemTotal($child);
                            $baseItemAmount -= $itemTotal;
                            $compensationTaxItemAmount -= (float)$child->getBaseDiscountTaxCompensationAmount();
                            $baseTaxItemAmount -= (float)$child->getBaseTaxAmount();
                        }
                    }
                }

                $spendItem = $this->spendItemFactory->create();
                $spendItems[] = $spendItem
                    ->setProductId((int)$quoteItem->getProduct()->getId())
                    ->setBaseAmount((float)$baseItemAmount)
                    ->setCompensationTaxAmount((float)$compensationTaxItemAmount)
                    ->setBaseTaxAmount((float)$baseTaxItemAmount)
                    ->setQty($quoteItem->getQty());
            }
        }

        $this->cartSpendRule->removeCurrentRuleIds();
        if ($this->spendRuleManagement->areThereEnabledRules([$websiteId])) {
            $spendItems = $this->spendRuleCalcManager->calculationByRules($spendItems, $customerId, $websiteId, $quote);
            $spendItemRules = $this->spendItemManager->getSpendItemRules($spendItems);
            $this->cartSpendRule->setCurrentRuleIds($spendItemRules);
        }

        $spendItems = $this->spendItemManager->calculateSpendItemsAmount($spendItems, $websiteId);
        $validItemsCount = count($spendItems);
        $baseItemsTotal = $this->spendItemManager->getSpendItemsTotalAmount($spendItems);
        $compensationTaxAmount = $this->spendItemManager->getSpendItemsTotalCompensationTaxAmount($spendItems);
        $baseTaxAmount = $this->spendItemManager->getSpendItemsTotalBaseTaxAmount($spendItems);

        $baseShippingAmount = .0;
        $maxBaseTotal = $baseItemsTotal;
        $shippingTaxAmount = .0;

        if ($this->config->isApplyingPointsToShipping($websiteId)) {
            if ($quoteAddress->getBaseShippingAmountForDiscount() > self::DELTA) {
                $baseShippingAmount = $quoteAddress->getBaseShippingAmountForDiscount();
            } else {
                $baseShippingAmount = $quoteAddress->getBaseShippingAmount();
            }
            $baseShippingAmount = (float)$baseShippingAmount;
            $shippingTaxAmount = (float)$quoteAddress->getBaseShippingTaxAmount();
            $maxShippingAmount = $baseShippingAmount - $quoteAddress->getBaseShippingDiscountAmount();
            $maxShippingAmount = $this->shareCoveredCalculator->calculateCoveredPrice($maxShippingAmount, $websiteId);

            $maxBaseTotal += $maxShippingAmount;
        }

        if ($this->config->isApplyingPointsToTax($websiteId)) {
            $baseTaxAmount += $compensationTaxAmount + $shippingTaxAmount;
            if (!$this->taxConfig->discountTax($quoteAddress->getQuote()->getStore()->getId())) {
                $maxBaseTotal += $this->shareCoveredCalculator->calculateCoveredPrice($baseTaxAmount, $websiteId);
            }
        }

        if (!$maxBaseTotal) {
            return $spendingData;
        }

        $availablePointsQty = $this->calculateAvailablePointsQty(
            $customerId,
            $websiteId,
            $maxBaseTotal,
            $pointsQtyToApply
        );

        if (!$availablePointsQty) {
            return $spendingData;
        }

        $maxBaseTotalCoveredByPoints = $this->rateCalculator->calculateRewardDiscount(
            $customerId,
            $availablePointsQty,
            $websiteId
        );

        $maxBaseTotalCoveredByPoints = min($maxBaseTotalCoveredByPoints, $maxBaseTotal);
        $maxTotalCoveredByPoints = $this->rateCalculator->convertCurrency($maxBaseTotalCoveredByPoints);

        if ($quote->getAwUseRewardPoints()) {
            $usedPoints =
                $quote->getAwRewardPoints() > $availablePointsQty
                    ? $availablePointsQty
                    : $quote->getAwRewardPoints();
            $usedPointsAmount =
                $quote->getAwRewardPointsAmount() > $maxTotalCoveredByPoints
                    ? $maxTotalCoveredByPoints
                    : $quote->getAwRewardPointsAmount();
            $baseUsedPointsAmount =
                $quote->getBaseAwRewardPointsAmount() > $maxBaseTotalCoveredByPoints
                    ? $maxBaseTotalCoveredByPoints
                    : $quote->getBaseAwRewardPointsAmount();

            $spendingData->setUsedPoints($usedPoints);
            $spendingData->setUsedPointsAmount($usedPointsAmount);
            $spendingData->setBaseUsedPointsAmount($baseUsedPointsAmount);
        }

        $spendingData->setSpendItems($spendItems);
        $spendingData->setBaseAvailablePointsAmount($maxBaseTotalCoveredByPoints);
        $spendingData->setAvailablePointsAmount($maxTotalCoveredByPoints);
        $spendingData->setAvailablePoints($availablePointsQty);
        $spendingData->setItemsCount($validItemsCount);
        $spendingData->setBaseItemsTotal($baseItemsTotal);
        $spendingData->setItemsTotal(
            $this->rateCalculator->convertCurrency($baseItemsTotal)
        );
        $spendingData->setBaseShippingAmount($baseShippingAmount);
        $spendingData->setShippingAmount(
            $this->rateCalculator->convertCurrency($baseShippingAmount)
        );
        $spendingData->setBaseTaxAmount($baseTaxAmount);
        $spendingData->setTaxAmount(
            $this->rateCalculator->convertCurrency($baseTaxAmount)
        );
        $spendingData->setLabelName($this->config->getLabelNameRewardPoints($websiteId));
        $spendingData->setTabLabelName($this->config->getTabLabelNameRewardPoints($websiteId));

        return $spendingData;
    }

    /**
     * Calculate available points qty
     *
     * @param int $customerId
     * @param int $websiteId
     * @param float $maxBaseTotal
     * @param int|null $pointsQtyToApply
     * @return int
     */
    private function calculateAvailablePointsQty($customerId, $websiteId, $maxBaseTotal, $pointsQtyToApply)
    {
        $availablePointsQtyByBalance = $this->rateCalculator->calculateSpendPoints(
            $customerId,
            $maxBaseTotal,
            $websiteId
        );
        $pointsQtyToApply = (isset($pointsQtyToApply) && $pointsQtyToApply > 0)
            ? $pointsQtyToApply
            : null;
        $availablePointsQtyByPointsQtyToApply = $this->rateCalculator->calculateSpendPoints(
            $customerId,
            $maxBaseTotal,
            $websiteId,
            $pointsQtyToApply
        );
        return (int)min($availablePointsQtyByBalance, $availablePointsQtyByPointsQtyToApply);
    }
}
