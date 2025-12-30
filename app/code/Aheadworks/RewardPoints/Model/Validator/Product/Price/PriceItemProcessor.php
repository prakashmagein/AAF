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

namespace Aheadworks\RewardPoints\Model\Validator\Product\Price;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\ConfigurationMismatchException;

class PriceItemProcessor implements PriceItemProcessorInterface
{
    /**
     * @param PriceItemProcessorPool $processorPool
     */
    public function __construct(
        private readonly PriceItemProcessorPool $processorPool
    ) {
    }

    /**
     * Has discount in product
     *
     * @param ProductInterface $product
     * @return bool
     * @throws ConfigurationMismatchException
     */
    public function hasDiscount(ProductInterface $product): bool
    {
        $productType = $product->getTypeId();
        $processor = $this->processorPool->getProcessorByCode($productType);

        return $processor->hasDiscount($product);
    }
}
