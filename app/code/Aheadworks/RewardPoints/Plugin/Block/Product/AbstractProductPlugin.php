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

use Aheadworks\RewardPoints\Block\ProductList\CategoryText;
use Aheadworks\RewardPoints\Block\ProductList\CategoryTextFactory;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Model\Product;

/**
 * Class AbstractProductPlugin
 * @package Aheadworks\RewardPoints\Plugin\Block
 */
class AbstractProductPlugin
{
    /**
     * @var CategoryTextFactory
     */
    private $categoryTextFactory;

    /**
     * @param CategoryTextFactory $categoryTextFactory
     */
    public function __construct(
        CategoryTextFactory $categoryTextFactory
    ) {
        $this->categoryTextFactory = $categoryTextFactory;
    }

    /**
     * Render category text if a product is valid
     *
     * @param AbstractProduct $subject
     * @param \Closure $proceed
     * @param Product $product
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetProductPrice($subject, $proceed, $product)
    {
        $html = $proceed($product);

        /** @var CategoryText $categoryText */
        $categoryText = $this->categoryTextFactory->create(
            [
                'data' => ['product' => $product]
            ]
        );

        $html .= $categoryText->toHtml();

        return $html;
    }
}
