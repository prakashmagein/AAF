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
namespace Aheadworks\RewardPoints\Plugin\Block\Product;

use Aheadworks\RewardPoints\Block\ProductList\Grouped\ProductText;
use Aheadworks\RewardPoints\Block\ProductList\Grouped\ProductTextFactory;
use Magento\GroupedProduct\Block\Product\View\Type\Grouped as GroupedType;
use Magento\Catalog\Model\Product;

/**
 * Class GroupedTypePlugin
 * @package Aheadworks\RewardPoints\Plugin\Block
 */
class GroupedTypePlugin
{
    /**
     * @var ProductTextFactory
     */
    private $productTextFactory;

    /**
     * @param ProductTextFactory $productTextFactory
     */
    public function __construct(
        ProductTextFactory $productTextFactory
    ) {
        $this->productTextFactory = $productTextFactory;
    }

    /**
     * Render product text if a product is valid
     *
     * @param GroupedType $subject
     * @param \Closure $proceed
     * @param Product $product
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetProductPrice($subject, $proceed, $product)
    {
        $html = $proceed($product);

        /** @var ProductText $productText */
        $productText = $this->productTextFactory->create(
            [
                'data' => ['product' => $product]
            ]
        );

        $html .= $productText->toHtml();

        return $html;
    }
}
