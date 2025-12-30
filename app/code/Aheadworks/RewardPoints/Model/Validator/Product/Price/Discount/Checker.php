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

namespace Aheadworks\RewardPoints\Model\Validator\Product\Price\Discount;

use Aheadworks\RewardPoints\Model\Config;
use Aheadworks\RewardPoints\Model\Validator\Product\Price\PriceItemProcessor;
use Exception;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\ConfigurationMismatchException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote\Item\AbstractItem as QuoteAbstractItem;
use Magento\Catalog\Api\ProductRepositoryInterface;

class Checker
{
    /**
     * @param Config $config
     * @param PriceItemProcessor $priceItemProcessorInterface
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        private readonly Config $config,
        private readonly PriceItemProcessor $priceItemProcessorInterface,
        private readonly ProductRepositoryInterface $productRepository
    ) {
    }

    /**
     * Validate product for spend points
     *
     * @param CartItemInterface|QuoteAbstractItem|ProductInterface $item
     * @return bool
     * @throws Exception
     */
    public function validateProductForSpend($item): bool
    {
        if (!$this->config->isRestrictSpendingPointsOnSale()) {
            return false;
        }

        $product = $item instanceof ProductInterface ? $item : $item->getProduct();
        if ($this->config->isRestrictSpendingPointsOnSale() &&
            $this->priceItemProcessorInterface->hasDiscount($product)
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check has discount
     *
     * @param ProductInterface $item
     * @return bool
     * @throws ConfigurationMismatchException
     */
    public function checkHasDiscount(ProductInterface $item): bool
    {
        if (!$this->config->isRestrictEarningPointsOnSale()) {
            return false;
        }

        if ($this->config->isRestrictEarningPointsOnSale() && $this->priceItemProcessorInterface->hasDiscount($item)) {
            return true;
        }

        return false;
    }

    /**
     * Check has discount by product ID
     *
     * @param int $productId
     * @return bool
     * @throws NoSuchEntityException|ConfigurationMismatchException
     */
    public function checkHasDiscountByProductId(int $productId): bool
    {
        $product = $this->productRepository->getById($productId);
        return $this->checkHasDiscount($product);
    }
}
