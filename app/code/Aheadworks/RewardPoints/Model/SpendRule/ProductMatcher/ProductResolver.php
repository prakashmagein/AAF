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

namespace Aheadworks\RewardPoints\Model\SpendRule\ProductMatcher;

use Aheadworks\RewardPoints\Model\SpendRule\ProductMatcher\ProductResolver\Pool;
use Magento\Catalog\Model\Product;

/**
 * Class ProductResolver
 */
class ProductResolver implements ProductResolverInterface
{
    /**
     * ProductResolver constructor.
     *
     * @param Pool $pool
     */
    public function __construct(private Pool $pool)
    {
    }

    /**
     * Get products for validation
     *
     * @param Product $product
     * @return Product[]
     * @throws \Exception
     */
    public function getProductsForValidation(Product $product): array
    {
        $resolver = $this->pool->getResolverByCode($product->getTypeId());
        return $resolver->getProductsForValidation($product);
    }
}
