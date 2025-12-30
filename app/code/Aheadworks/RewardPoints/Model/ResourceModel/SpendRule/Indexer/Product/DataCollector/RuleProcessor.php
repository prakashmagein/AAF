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

namespace Aheadworks\RewardPoints\Model\ResourceModel\SpendRule\Indexer\Product\DataCollector;

use Aheadworks\RewardPoints\Api\Data\SpendRuleInterface;
use Aheadworks\RewardPoints\Model\SpendRule\ProductMatcher\Result as ProductMatcherResult;
use Aheadworks\RewardPoints\Model\SpendRule\ProductMatcher;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Class RuleProcessor
 */
class RuleProcessor
{
    /**
     * @param ProductMatcher $productMatcher
     * @param ProductMatcherItemsProcessor $productMatcherItemsProcessor
     */
    public function __construct(
        private ProductMatcher $productMatcher,
        private ProductMatcherItemsProcessor $productMatcherItemsProcessor
    ) {
    }

    /**
     * Prepare rule product data for all matching products
     *
     * @param SpendRuleInterface $rule
     * @return array
     */
    public function getAllMatchingProductsData(SpendRuleInterface $rule): array
    {
        $data = [];
        /** @var ProductMatcherResult $result */
        $result = $this->productMatcher->matchAllProducts($rule);
        if ($result->getTotalCount() > 0) {
            $data = $this->productMatcherItemsProcessor->prepareData($result->getItems(), $rule);
        }

        return $data;
    }

    /**
     * Prepare rule product data for specific product
     *
     * @param SpendRuleInterface $rule
     * @param ProductInterface $product
     * @return array
     */
    public function getMatchingProductData(SpendRuleInterface $rule, ProductInterface $product): array
    {
        $data = [];
        /** @var ProductMatcherResult $result */
        $result = $this->productMatcher->matchProduct($product, $rule);
        if ($result->getTotalCount() > 0) {
            $data = $this->productMatcherItemsProcessor->prepareData($result->getItems(), $rule);
        }

        return $data;
    }
}
