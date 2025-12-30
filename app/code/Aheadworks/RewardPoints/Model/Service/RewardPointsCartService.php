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

namespace Aheadworks\RewardPoints\Model\Service;

use Aheadworks\RewardPoints\Api\RewardPointsCartManagementInterface;
use Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface;
use Aheadworks\RewardPoints\Model\Config;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Api\CustomAttributesDataInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\RewardPoints\Model\Service\RewardPointsCartService\SpendingData\Provider
    as SpendingDataProvider;
use Magento\Quote\Model\Quote\TotalsCollector as QuoteTotalsCollector;
use Magento\Quote\Model\Quote;
use Aheadworks\RewardPoints\Api\Data\CustomerCartMetadataInterface;
use Aheadworks\RewardPoints\Api\Data\CustomerCartMetadataInterfaceFactory;

class RewardPointsCartService implements RewardPointsCartManagementInterface
{
    /**
     * Quote data flag to check if quote totals have been already collected before points statistics calculation
     */
    public const ARE_QUOTE_TOTALS_COLLECTED_FLAG = 'are_quote_totals_collected_flag';

    /**
     * @param CustomerRewardPointsManagementInterface $customerRewardPointsService
     * @param CartRepositoryInterface $quoteRepository
     * @param Config $config
     * @param SpendingDataProvider $spendingDataProvider
     * @param QuoteTotalsCollector $quoteTotalsCollector
     * @param CustomerCartMetadataInterfaceFactory $customerCartMetadataFactory
     */
    public function __construct(
        private readonly CustomerRewardPointsManagementInterface $customerRewardPointsService,
        private readonly CartRepositoryInterface $quoteRepository,
        private readonly Config $config,
        private readonly SpendingDataProvider $spendingDataProvider,
        private readonly QuoteTotalsCollector $quoteTotalsCollector,
        private readonly CustomerCartMetadataInterfaceFactory $customerCartMetadataFactory
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function get($cartId)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }
        return $quote->getAwUseRewardPoints();
    }

    /**
     * Adds a reward points to a specified cart.
     *
     * @param  int $cartId
     * @param  int $pointsQty
     * @return array
     * @throws NoSuchEntityException The specified cart does not exist.
     * @throws CouldNotSaveException The specified reward points not be added.
     */
    public function set($cartId, $pointsQty)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }
        $websiteId = (int)$quote->getStore()->getWebsiteId();
        $onceMinBalance = $this->customerRewardPointsService->getCustomerRewardPointsOnceMinBalance(
            $quote->getCustomerId(),
            $quote->getStore()->getWebsiteId()
        );
        if (!$quote->getCustomerId()
            || !$this->customerRewardPointsService->getCustomerRewardPointsBalance($quote->getCustomerId())
            || $onceMinBalance
        ) {
            throw new NoSuchEntityException(__('No 1% to be used',
                    $this->config->getLabelNameRewardPoints($websiteId)
                )
            );
        }

        $quote->getShippingAddress()->setCollectShippingRates(true);
        try {
            $quote->setAwUseRewardPoints(true);
            $quote->setAwRewardPointsQtyToApply($pointsQty);
            $this->quoteRepository->save($quote->collectTotals());
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not apply %1',
                    $this->config->getLabelNameRewardPoints($websiteId)
                )
            );
        }

        if (!$quote->getAwUseRewardPoints()) {
            throw new NoSuchEntityException(__('No possibility to use %1 discounts in the cart',
                    $this->config->getLabelNameRewardPoints($websiteId)
                )
            );
        }

        return [
            CustomAttributesDataInterface::CUSTOM_ATTRIBUTES => [
                'success' => true,
                'message' => $this->getMessage($quote)
            ]
        ];
    }

    /**
     * Deletes a reward points from a specified cart.
     *
     * @param  int $cartId
     * @return boolean
     * @throws NoSuchEntityException The specified cart does not exist.
     * @throws CouldNotDeleteException The specified reward points could not be deleted.
     */
    public function remove($cartId)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }

        $quote->getShippingAddress()->setCollectShippingRates(true);
        try {
            $quote->setAwUseRewardPoints(false);
            $quote->setAwRewardPointsQtyToApply(0);
            $this->quoteRepository->save($quote->collectTotals());
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not remove %1',
                    $this->config->getLabelNameRewardPoints((int)$quote->getStore()->getWebsiteId())
                )
            );
        }
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getCustomerCartMetadata($customerId, $cartId)
    {
        /** @var Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        $websiteId = $quote->getStore()->getWebsiteId();

        if (!$quote->getData(self::ARE_QUOTE_TOTALS_COLLECTED_FLAG)) {
            $this->quoteTotalsCollector->collect($quote);
            $quote->setData(self::ARE_QUOTE_TOTALS_COLLECTED_FLAG, true);
        }

        $spendingData = $this->spendingDataProvider->getDataByQuote($quote);

        $customerRewardPointsDetails = $this->customerRewardPointsService->getCustomerRewardPointsDetails(
            $customerId,
            $websiteId
        );

        return $this->customerCartMetadataFactory->create(
            [
                'data' => [
                    CustomerCartMetadataInterface::REWARD_POINTS_BALANCE_QTY =>
                        $customerRewardPointsDetails->getCustomerRewardPointsBalance(),
                    CustomerCartMetadataInterface::CAN_APPLY_REWARD_POINTS =>
                        ($customerRewardPointsDetails->getCustomerRewardPointsOnceMinBalance() == 0)
                        && $customerRewardPointsDetails->isCustomerRewardPointsSpendRateByGroup()
                        && $customerRewardPointsDetails->isCustomerRewardPointsSpendRate()
                        && ($spendingData->getAvailablePoints() > 0),
                    CustomerCartMetadataInterface::REWARD_POINTS_MAX_ALLOWED_QTY_TO_APPLY =>
                        $spendingData->getAvailablePoints(),
                    CustomerCartMetadataInterface::REWARD_POINTS_CONVERSION_RATE_POINT_TO_CURRENCY_VALUE =>
                        $customerRewardPointsDetails->getCustomerConversionRatePointToCurrencyValue(),
                    CustomerCartMetadataInterface::ARE_REWARD_POINTS_APPLIED =>
                        ($spendingData->getUsedPoints() > 0),
                    CustomerCartMetadataInterface::APPLIED_REWARD_POINTS_QTY =>
                        $spendingData->getUsedPoints(),
                    CustomerCartMetadataInterface::APPLIED_REWARD_POINTS_AMOUNT =>
                        $spendingData->getUsedPointsAmount(),
                    CustomerCartMetadataInterface::REWARD_POINTS_LABEL_NAME =>
                        $spendingData->getLabelName(),
                    CustomerCartMetadataInterface::REWARD_POINTS_TAB_LABEL_NAME =>
                        $spendingData->getTabLabelName()
                ]
            ]
        );
    }

    /**
     * Retrieves message to show customer
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return string
     */
    private function getMessage($quote)
    {
        $websiteId = (int)$quote->getStore()->getWebsiteId();
        $shareCoveredValue = $this->config->getShareCoveredValue($quote->getStore()->getWebsiteId());
        if ($shareCoveredValue && ($shareCoveredValue != 100)) {
            $message = __('%1 were successfully applied. Important: points can not cover the whole purchase.',
                $this->config->getLabelNameRewardPoints($websiteId)
            );
        } else {
            $message = __('%1 were successfully applied.', $this->config->getLabelNameRewardPoints($websiteId));
        }
        return $message;
    }
}
