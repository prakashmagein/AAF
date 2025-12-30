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
namespace Aheadworks\RewardPoints\Model\EarnRule\ProductMatcher;

use Aheadworks\RewardPoints\Model\EarnRule\ProductMatcher\ProductResolver\Pool;
use Magento\Catalog\Model\Product;

/**
 * Class ProductResolver
 * @package Aheadworks\RewardPoints\Model\EarnRule\ProductMatcher
 */
class ProductResolver implements ProductResolverInterface
{
    /**
     * @var Pool
     */
    private $pool;

    /**
     * @param Pool $pool
     */
    public function __construct(
        Pool $pool
    ) {
        $this->pool = $pool;
    }

    /**
     * Get products for validation
     *
     * @param Product $product
     * @return Product[]
     * @throws \Exception
     */
    public function getProductsForValidation($product)
    {
        $resolver = $this->pool->getResolverByCode($product->getTypeId());
        return $resolver->getProductsForValidation($product);
    }
}
