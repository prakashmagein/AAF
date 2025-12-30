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

namespace Aheadworks\RewardPoints\Model\ResourceModel\SpendRule\Indexer\Product;

use Aheadworks\RewardPoints\Api\SpendRuleManagementInterface;
use Aheadworks\RewardPoints\Model\Indexer\SpendRule\ProductLoader;
use Aheadworks\RewardPoints\Model\ResourceModel\SpendRule\Indexer\Product\DataCollector\RuleProcessor;

/**
 * Class DataCollector
 */
class DataCollector
{
    /**
     * @param SpendRuleManagementInterface $spendRuleManagement
     * @param RuleProcessor $ruleProcessor
     * @param ProductLoader $productLoader
     */
    public function __construct(
        private SpendRuleManagementInterface $spendRuleManagement,
        private RuleProcessor $ruleProcessor,
        private ProductLoader $productLoader
    ) {
    }

    /**
     * Get full index data
     *
     * @return array
     */
    public function getAllData(): array
    {
        $rules = $this->spendRuleManagement->getActiveRulesForIndexer();
        $result = [];
        foreach ($rules as $rule) {
            $result[] = $this->ruleProcessor->getAllMatchingProductsData($rule);
        }

        return array_merge(...$result);
    }

    /**
     * Get index data for specified product ids
     *
     * @param int[] $productIds
     * @return array
     */
    public function getDataToUpdate(array $productIds): array
    {
        $rules = $this->spendRuleManagement->getActiveRules();
        $products = $this->productLoader->getProducts($productIds);
        $result = [];
        foreach ($rules as $rule) {
            foreach ($products as $product) {
                $result[] = $this->ruleProcessor->getMatchingProductData($rule, $product);
            }
        }

        return array_merge(...$result);
    }
}
