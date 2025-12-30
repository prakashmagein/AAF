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
namespace Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ProductProcessor;

use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Catalog\Model\Product;

/**
 * Class CatalogPriceCalculator
 * @package Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ProductProcessor
 */
class CatalogPriceCalculator
{
    /**
     * @var CatalogHelper
     */
    private $catalogHelper;

    /**
     * @param CatalogHelper $catalogHelper
     */
    public function __construct(
        CatalogHelper $catalogHelper
    ) {
        $this->catalogHelper = $catalogHelper;
    }
    /**
     * Get final price
     *
     * @param Product $product
     * @param float $price
     * @param bool $exclTax
     * @return float
     */
    public function getFinalPriceAmount($product, $price, $exclTax = true)
    {
        $includingTax = !$exclTax;
        $finalPrice = $this->catalogHelper->getTaxPrice(
            $product,
            $price,
            $includingTax,
            null,
            null,
            null,
            null,
            null,
            true
        );

        return $finalPrice;
    }
}
