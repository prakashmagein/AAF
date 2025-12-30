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

namespace Aheadworks\RewardPoints\Model\Calculator;

use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItem;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemInterface;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemsResolver;
use Aheadworks\RewardPoints\Model\Calculator\Earning\Calculator\General as GeneralCalculator;
use Aheadworks\RewardPoints\Model\Calculator\Earning\Predictor;
use Aheadworks\RewardPoints\Model\Source\Calculation\PointsEarning;
use Aheadworks\RewardPoints\Model\Calculator\Earning\Calculator\CalculationRequestInterface;
use Aheadworks\RewardPoints\Model\Calculator\Earning\Calculator\CalculationRequestInterfaceFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Aheadworks\RewardPoints\Model\Config;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface as Logger;

class Earning
{
    /**
     * @param Config $config
     * @param EarnItemsResolver $earnItemsResolver
     * @param Predictor $predictor
     * @param StoreManagerInterface $storeManager
     * @param Logger $logger
     * @param CalculationRequestInterfaceFactory $calculationRequestFactory
     * @param GeneralCalculator $generalCalculator
     * @param Validator $validator
     */
    public function __construct(
        private readonly Config $config,
        private readonly EarnItemsResolver $earnItemsResolver,
        private readonly Predictor $predictor,
        private readonly StoreManagerInterface $storeManager,
        private readonly Logger $logger,
        private readonly CalculationRequestInterfaceFactory $calculationRequestFactory,
        private readonly GeneralCalculator $generalCalculator,
        private readonly Validator $validator
    ) {
    }

    /**
     * Retrieve calculation earning points value by quote
     *
     * @param CartInterface $quote
     * @param int|null $customerId
     * @return ResultInterface
     * @throws \Zend_Db_Select_Exception
     */
    public function calculationByQuote(CartInterface $quote, ?int $customerId): ResultInterface
    {
        $websiteId = $this->getCurrentWebsiteId();

        if (!$websiteId) {
            return $this->generalCalculator->getEmptyResult();
        }
        if ($this->config->areRestrictEarningPointsWithCartPriceRules($websiteId) &&
            $this->validator->canApplySalesRules($quote)) {

            return $this->generalCalculator->getEmptyResult();
        }

        $beforeTax = $this->getBeforeTax($websiteId);

        try {
            /** @var EarnItem[] $items */
            $items = $this->earnItemsResolver->getItemsByQuote($quote, $beforeTax);
            if (!$customerId) {
                $customerGroupId = $this->config->getDefaultCustomerGroupIdForGuest();
                $result = $this->generalCalculator->calculateByCustomerGroup($items, (int)$customerGroupId, $websiteId);
            } else {
                $result = $this->generalCalculator->calculate($items, (int)$customerId, $websiteId);
            }
            $calculationRequest = $this->prepareCalculationRequest(
                $customerId ?? null,
                $customerGroupId ?? null,
                $websiteId,
                $items,
                $quote,
                $result->getPoints()
            );

            $result = $this->generalCalculator->calculatePointsByRules($result, $calculationRequest);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            $result = $this->generalCalculator->getEmptyResult();
        }

        return $result;
    }

    /**
     * Retrieve calculation earning points value by invoice
     *
     * @param InvoiceInterface $invoice
     * @param int $customerId
     * @param CartInterface $quote
     * @param null $websiteId
     * @return ResultInterface
     * @throws \Exception
     */
    public function calculationByInvoice(
        InvoiceInterface $invoice,
        int $customerId,
        CartInterface $quote,
        $websiteId = null
    ): ResultInterface {
        $websiteId = $websiteId ? $websiteId : $this->getCurrentWebsiteId();

        if (!$websiteId) {
            return $this->generalCalculator->getEmptyResult();
        }
        $beforeTax = $this->getBeforeTax($websiteId);

        /** @var EarnItem[] $items */
        $items = $this->earnItemsResolver->getItemsByInvoice($invoice, $beforeTax);
        $result = $this->generalCalculator->calculate($items, (int)$customerId, $websiteId);
        $calculationRequest = $this->prepareCalculationRequest(
            $customerId,
            null,
            $websiteId,
            $items,
            $quote,
            $result->getPoints(),
            true,
            (int)$invoice->getOrderId(),
            false,
            true
        );

        $result = $this->generalCalculator->calculatePointsByRules($result, $calculationRequest);

        return $result;
    }

    /**
     * Retrieve calculation earning points value by credit memo
     *
     * @param CreditmemoInterface $creditmemo
     * @param int $customerId
     * @param CartInterface $quote
     * @param int|null $websiteId
     * @return ResultInterface
     */
    public function calculationByCreditmemo(
        CreditmemoInterface $creditmemo,
        int                 $customerId,
        CartInterface       $quote,
                            $websiteId = null
    ): ResultInterface {

        $websiteId = $websiteId ? $websiteId : $this->getCurrentWebsiteId();

        if (!$websiteId) {
            return $this->generalCalculator->getEmptyResult();
        }
        $beforeTax = $this->getBeforeTax($websiteId);

        /** @var EarnItem[] $items */
        $items = $this->earnItemsResolver->getItemsByCreditmemo($creditmemo, $beforeTax);
        $result = $this->generalCalculator->calculate($items, (int)$customerId, $websiteId);

        $calculationRequest = $this->prepareCalculationRequest(
            $customerId,
            null,
            $websiteId,
            $items,
            $quote,
            $result->getPoints(),
            true,
            (int)$creditmemo->getOrderId(),
            true
        );
        $result = $this->generalCalculator->calculatePointsByRules($result, $calculationRequest);

        return $result;
    }

    /**
     * Retrieve calculation earning points value by product.
     *
     * @param ProductInterface $product
     * @param bool $mergeRuleIds
     * @param int|null $customerId
     * @param int|null $websiteId
     * @param int|null $customerGroupId
     * @return ResultInterface
     */
    public function calculationByProduct(
        ProductInterface $product,
        bool             $mergeRuleIds,
        $customerId,
        $websiteId = null,
        $customerGroupId = null
    ): ResultInterface {

        $websiteId = $websiteId ? $websiteId : $this->getCurrentWebsiteId();

        if (!$websiteId) {
            return $this->generalCalculator->getEmptyResult();
        }
        $beforeTax = $this->getBeforeTax($websiteId);
        try {
            /** @var EarnItem[] $items */
            $items = $this->earnItemsResolver->getItemsByProduct($product, $beforeTax);
            if (isset($customerGroupId)) {
                $result = $this->predictor->calculateMaxPointsForCustomerGroup(
                    $items,
                    $websiteId,
                    (int)$customerGroupId,
                    $mergeRuleIds
                );
            } elseif ($customerId) {
                $result = $this->predictor->calculateMaxPointsForCustomer(
                    $items,
                    $customerId,
                    $websiteId,
                    $mergeRuleIds
                );
            } else {
                $result = $this->predictor->calculateMaxPointsForGuest($items, $websiteId, $mergeRuleIds);
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            $result = $this->generalCalculator->getEmptyResult();
        }

        return $result;
    }

    /**
     * Get current website
     *
     * @return int|null
     */
    private function getCurrentWebsiteId()
    {
        try {
            $currentWebsite = $this->storeManager->getWebsite();
        } catch (LocalizedException $e) {
            return null;
        }
        return (int)$currentWebsite->getId();
    }

    /**
     * Get BeforeTax
     *
     * @param  int|null $websiteId
     * @return bool
     */
    private function getBeforeTax(?int $websiteId): bool
    {
        return $this->config->getPointsEarningCalculation($websiteId) == PointsEarning::BEFORE_TAX;
    }

    /**
     * Prepare calculation request
     *
     * @param int|null $customerId
     * @param int|null $customerGroupId
     * @param int|null $websiteId
     * @param EarnItemInterface $items
     * @param CartInterface $quote
     * @param float $points
     * @param bool $isNeedCalculateCartRule
     * @param int|null $orderId
     * @param bool $calculateForCreditMemo
     * @param bool $calculateForInvoice
     * @return CalculationRequestInterface
     */
    public function prepareCalculationRequest(
        $customerId,
        $customerGroupId,
        $websiteId,
        $items,
        $quote,
        $points,
        $isNeedCalculateCartRule = true,
        $orderId = null,
        $calculateForCreditMemo = false,
        $calculateForInvoice = false
    ): CalculationRequestInterface {

        /** @var CalculationRequestInterface $calculationRequest */
        $calculationRequest = $this->calculationRequestFactory->create();

        $calculationRequest->setCustomerId((int) $customerId);
        $calculationRequest->setCustomerGroupId($customerGroupId);
        $calculationRequest->setItems($items);
        $calculationRequest->setWebsiteId($websiteId);
        $calculationRequest->setQuote($quote);
        $calculationRequest->setPoints($points);
        $calculationRequest->setIsNeedCalculateCartRule($isNeedCalculateCartRule);
        $calculationRequest->setOrderId($orderId);
        $calculationRequest->setIsCalculateForCreditMemo($calculateForCreditMemo);
        $calculationRequest->setIsCalculateForInvoice($calculateForInvoice);

        return $calculationRequest;
    }
}
