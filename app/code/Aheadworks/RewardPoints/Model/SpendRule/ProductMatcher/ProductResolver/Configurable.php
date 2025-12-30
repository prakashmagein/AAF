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

namespace Aheadworks\RewardPoints\Model\SpendRule\ProductMatcher\ProductResolver;

use Aheadworks\RewardPoints\Model\SpendRule\ProductMatcher\ProductResolverInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;

/**
 * Class Configurable
 */
class Configurable implements ProductResolverInterface
{
    /**
     * Get products for validation
     *
     * @param Product $product
     * @return ProductInterface[]|Product[]
     */
    public function getProductsForValidation(Product $product): array
    {
        /** @var ConfigurableType $configurableType */
        $configurableType = $product->getTypeInstance();
        $productsForValidation = $configurableType->getUsedProducts($product);
        $productsForValidation[] = $product;

        return $productsForValidation;
    }
}
