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
namespace Aheadworks\RewardPoints\Model\EarnRule\ProductMatcher\ProductResolver;

use Aheadworks\RewardPoints\Model\EarnRule\ProductMatcher\ProductResolverInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;

/**
 * Class Configurable
 * @package Aheadworks\RewardPoints\Model\EarnRule\ProductMatcher\ProductResolver
 */
class Configurable implements ProductResolverInterface
{
    /**
     * Get products for validation
     *
     * @param Product $product
     * @return ProductInterface[]|Product[]
     */
    public function getProductsForValidation($product)
    {
        /** @var ConfigurableType $configurableType */
        $configurableType = $product->getTypeInstance();
        $productsForValidation = $configurableType->getUsedProducts($product);
        $productsForValidation[] = $product;

        return $productsForValidation;
    }
}
