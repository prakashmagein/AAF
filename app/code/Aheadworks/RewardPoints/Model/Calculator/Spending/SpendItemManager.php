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

namespace Aheadworks\RewardPoints\Model\Calculator\Spending;

use Aheadworks\RewardPoints\Model\Calculator\Spending\SpendItemInterface;
use Aheadworks\RewardPoints\Model\Calculator\ShareCoveredCalculator;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Class SpendItemManager
 */
class SpendItemManager
{
    /**
     * SpendItemManager constructor.
     *
     * @param ShareCoveredCalculator $shareCoveredCalculator
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        private ShareCoveredCalculator $shareCoveredCalculator,
        private ProductRepositoryInterface $productRepository
    ) {
    }

    /**
     * Get spend items total amount
     *
     * @param SpendItemInterface[] $spendItems
     * @return float
     */
    public function getSpendItemsTotalAmount(array $spendItems): float
    {
        $result = 0;
        /** @var SpendItemInterface $spendItem */
        foreach ($spendItems as $spendItem) {
            $result += (float)$spendItem->getBaseAmount();
        }
        return $result;
    }

    /**
     * Get spend items total compensation tax amount
     *
     * @param SpendItemInterface[] $spendItems
     * @return float
     */
    public function getSpendItemsTotalCompensationTaxAmount(array $spendItems): float
    {
        $result = 0;
        /** @var SpendItemInterface $spendItem */
        foreach ($spendItems as $spendItem) {
            $result += (float)$spendItem->getCompensationTaxAmount();
        }
        return $result;
    }

    /**
     * Get spend items total base tax amount
     *
     * @param SpendItemInterface[] $spendItems
     * @return float
     */
    public function getSpendItemsTotalBaseTaxAmount(array $spendItems): float
    {
        $result = 0;
        /** @var SpendItemInterface $spendItem */
        foreach ($spendItems as $spendItem) {
            $result += (float)$spendItem->getBaseTaxAmount();
        }
        return $result;
    }

    /**
     * Get spend item rules
     *
     * @param SpendItemInterface[] $spendItems
     * @return array
     */
    public function getSpendItemRules(array $spendItems): array
    {
        $result = [];
        foreach ($spendItems as $spendItem) {
            $result += (array)$spendItem->getAppliedRuleIds();
        }
        return array_unique($result);
    }

    /**
     * Calculate spend items amount
     *
     * @param SpendItemInterface[] $spendItems
     * @param int $websiteId
     * @return array
     */
    public function calculateSpendItemsAmount(array $spendItems, int $websiteId): array
    {
        foreach ($spendItems as $spendItem) {
            $product = $this->productRepository->getById($spendItem->getProductId());
            $baseItemAmount = $this->shareCoveredCalculator->calculateCoveredPrice(
                $spendItem->getBaseAmount(),
                $websiteId,
                $product,
                $spendItem->getShareCoveredPercent()
            );
            $spendItem->setBaseAmount($baseItemAmount);
        }
        return $spendItems;
    }
}
