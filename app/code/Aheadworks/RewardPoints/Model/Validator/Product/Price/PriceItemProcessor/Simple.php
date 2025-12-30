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
namespace Aheadworks\RewardPoints\Model\Validator\Product\Price\PriceItemProcessor;

use Magento\Catalog\Api\Data\ProductInterface;
use Aheadworks\RewardPoints\Model\Validator\Product\Price\PriceItemProcessorInterface;

/**
 * Class Simple
 */
class Simple implements PriceItemProcessorInterface
{
    /**
     * Has discount in product
     *
     * @param $product
     * @return bool
     */
    public function hasDiscount(ProductInterface $product): bool
    {
        $regularPrice = $product->getPriceInfo()->getPrice('regular_price')->getValue();
        $finalPrice = $product->getPriceInfo()->getPrice('final_price')->getValue();
        return $finalPrice < $regularPrice;
    }
}
