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
namespace Aheadworks\RewardPoints\Model\ResourceModel\EarnRule\Indexer\Product;

use Aheadworks\RewardPoints\Api\Data\EarnRuleInterface;
use Aheadworks\RewardPoints\Api\EarnRuleManagementInterface;
use Aheadworks\RewardPoints\Model\Indexer\EarnRule\ProductLoader;
use Aheadworks\RewardPoints\Model\ResourceModel\EarnRule\Indexer\Product\DataCollector\RuleProcessor;

/**
 * Class DataCollector
 * @package Aheadworks\RewardPoints\Model\ResourceModel\EarnRule\Indexer\Product
 */
class DataCollector
{
    /**
     * @var EarnRuleManagementInterface
     */
    private $earnRuleManagement;

    /**
     * @var RuleProcessor
     */
    private $ruleProcessor;

    /**
     * @var ProductLoader
     */
    private $productLoader;

    /**
     * @param EarnRuleManagementInterface $earnRuleManagement
     * @param RuleProcessor $ruleProcessor
     * @param ProductLoader $productLoader
     */
    public function __construct(
        EarnRuleManagementInterface $earnRuleManagement,
        RuleProcessor $ruleProcessor,
        ProductLoader $productLoader
    ) {
        $this->earnRuleManagement = $earnRuleManagement;
        $this->ruleProcessor = $ruleProcessor;
        $this->productLoader = $productLoader;
    }

    /**
     * Get full index data
     *
     * @return array
     */
    public function getAllData(): array
    {
        $rules = $this->earnRuleManagement->getActiveRulesForIndexer();
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
        $rules = $this->earnRuleManagement->getActiveRules([]);
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
