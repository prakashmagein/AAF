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

namespace Aheadworks\RewardPoints\Model\SpendRule;

use Aheadworks\RewardPoints\Api\Data\SpendRuleInterface;
use Aheadworks\RewardPoints\Model\SpendRule\Condition\Rule\Loader as ConditionRuleLoader;
use Aheadworks\RewardPoints\Model\SpendRule\ProductMatcher\ProductResolver;
use Aheadworks\RewardPoints\Model\SpendRule\ProductMatcher\Result;
use Aheadworks\RewardPoints\Model\SpendRule\ProductMatcher\ResultFactory;
use Aheadworks\RewardPoints\Model\SpendRule\ProductMatcher\Result\Item as ResultItem;
use Aheadworks\RewardPoints\Model\SpendRule\ProductMatcher\Result\ItemFactory as ResultItemFactory;
use Magento\Catalog\Model\Product;

/**
 * Class ProductMatcher
 */
class ProductMatcher
{
    /**
     * ProductMatcher constructor.
     *
     * @param ProductResolver $productResolver
     * @param ConditionRuleLoader $conditionRuleLoader
     * @param ResultFactory $resultFactory
     * @param ResultItemFactory $resultItemFactory
     */
    public function __construct(
        private ProductResolver $productResolver,
        private ConditionRuleLoader $conditionRuleLoader,
        private ResultFactory $resultFactory,
        private ResultItemFactory $resultItemFactory
    ) {
    }

    /**
     * Match the product with the specified rule
     *
     * @param Product $product
     * @param SpendRuleInterface $rule
     * @return Result
     */
    public function matchProduct(Product $product, SpendRuleInterface $rule): Result
    {
        /** @var Result $result */
        $result = $this->resultFactory->create();
        $items = [];

        $conditionRule = $this->conditionRuleLoader->loadRule($rule->getCondition());
        $productsForValidation = $this->productResolver->getProductsForValidation($product);
        $isValid = false;
        foreach ($productsForValidation as $productForValidation) {
            if ($conditionRule->validate($productForValidation)) {
                $isValid = true;
                break;
            }
        }
        if ($isValid) {
            $productId = $product->getId();

            $websiteIds = [];
            foreach ($rule->getWebsiteIds() as $ruleWebsiteId) {
                if (in_array($ruleWebsiteId, $product->getWebsiteIds())) {
                    $websiteIds[] = $ruleWebsiteId;
                }
            }

            if (count($websiteIds) >0) {
                /** @var ResultItem $resultItem */
                $resultItem = $this->resultItemFactory->create();
                $resultItem
                    ->setProductId($productId)
                    ->setWebsiteIds($websiteIds);

                $items[] = $resultItem;
            }
        }

        $result
            ->setItems($items)
            ->setTotalCount(count($items));

        return $result;
    }

    /**
     * Get matching products validation data
     *
     * @param SpendRuleInterface $rule
     * @return Result
     */
    public function matchAllProducts(SpendRuleInterface $rule): Result
    {
        /** @var Result $result */
        $result = $this->resultFactory->create();
        $items = [];

        $websiteIds = $rule->getWebsiteIds();
        $conditionRule = $this->conditionRuleLoader->loadRule($rule->getCondition());
        $matchingProductsData = $conditionRule->getMatchingProductIds($websiteIds);

        foreach ($matchingProductsData as $productId => $validatedWebsiteIds) {
            $websiteIds = [];
            foreach ($validatedWebsiteIds as $websiteId => $isWebsiteValid) {
                if ($isWebsiteValid) {
                    $websiteIds[] = $websiteId;
                }
            }
            if (count($websiteIds) >0) {
                /** @var ResultItem $resultItem */
                $resultItem = $this->resultItemFactory->create();
                $resultItem
                    ->setProductId($productId)
                    ->setWebsiteIds($websiteIds);

                $items[] = $resultItem;
            }
        }

        $result
            ->setItems($items)
            ->setTotalCount(count($items));

        return $result;
    }
}
