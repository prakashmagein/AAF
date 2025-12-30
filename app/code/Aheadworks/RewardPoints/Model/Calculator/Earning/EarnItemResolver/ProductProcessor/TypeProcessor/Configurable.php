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
namespace Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ProductProcessor\TypeProcessor;

use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ProductProcessor\CatalogPriceCalculator;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ProductProcessor\TypeProcessorInterface;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemInterface;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemInterfaceFactory;
use Aheadworks\RewardPoints\Model\Validator\Product\Price\Discount\Checker as DiscountChecker;

/**
 * Class Configurable
 */
class Configurable implements TypeProcessorInterface
{
    /**
     * @var EarnItemInterfaceFactory
     */
    private $earnItemFactory;

    /**
     * @var CatalogPriceCalculator
     */
    private $catalogPriceCalculator;

    /**
     * @var DiscountChecker
     */
    private $discountChecker;

    /**
     * @param EarnItemInterfaceFactory $earnItemFactory
     * @param CatalogPriceCalculator $catalogPriceCalculator
     * @param DiscountChecker $discountChecker
     */
    public function __construct(
        EarnItemInterfaceFactory $earnItemFactory,
        CatalogPriceCalculator $catalogPriceCalculator,
        DiscountChecker $discountChecker
    ) {
        $this->earnItemFactory = $earnItemFactory;
        $this->catalogPriceCalculator = $catalogPriceCalculator;
        $this->discountChecker = $discountChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function getEarnItems($product, $beforeTax = true)
    {
        $earnItems = [];

        if ($this->discountChecker->checkHasDiscount($product)) {
            return $earnItems;
        }

        $children = $product->getTypeInstance()
            ->getUsedProducts($product);

        foreach ($children as $child) {
            /** @var EarnItemInterface $earnItem */
            $earnItem = $this->earnItemFactory->create();

            $price = $this->catalogPriceCalculator->getFinalPriceAmount(
                $child,
                $child->getFinalPrice(),
                $beforeTax
            );

            $earnItem
                ->setProductId($child->getId())
                ->setBaseAmount($price)
                ->setQty(1);

            $earnItems[] = $earnItem;
        }

        return $earnItems;
    }
}
