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

namespace Aheadworks\RewardPoints\Model\Validator\Product\Price\PriceItemProcessor;

use Aheadworks\RewardPoints\Model\Validator\Product\Price\PriceItemProcessorInterface;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Class Configurable
 */
class Configurable implements PriceItemProcessorInterface
{
    /**
     * Has discount in product
     *
     * @param ProductInterface $product
     * @return bool
     */
    public function hasDiscount(ProductInterface $product): bool
    {
        $basePrice = $product->getPriceInfo()->getPrice('regular_price');

        $regularPrice = $basePrice->getMinRegularAmount()->getValue();
        $finalPrice = $product->getFinalPrice();

        return $finalPrice < $regularPrice;
    }
}
