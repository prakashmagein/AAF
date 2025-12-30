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

namespace Aheadworks\RewardPoints\Model\Calculator\Spending\Validator;

use Aheadworks\RewardPoints\Model\Validator\Product\Price\Discount\Checker as DiscountPriceChecker;
use Magento\Framework\Validator\AbstractValidator;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote\Item\AbstractItem as QuoteAbstractItem;

class Discount extends AbstractValidator
{
    /**
     * @var DiscountPriceChecker
     */
    private $discountPriceChecker;

    /**
     * @param DiscountPriceChecker $discountPriceChecker
     */
    public function __construct(
        DiscountPriceChecker $discountPriceChecker
    ) {
        $this->discountPriceChecker = $discountPriceChecker;
    }

    /**
     * Returns true if and only if quote item entity meets the validation requirements
     *
     * @param CartItemInterface|QuoteAbstractItem $quoteItem
     * @return bool
     */
    public function isValid($quoteItem)
    {
        if ($this->discountPriceChecker->validateProductForSpend($quoteItem)) {
            $this->_addMessages([
                __('Applying points on discount products are disabled')
            ]);
            return false;
        }

        return true;
    }
}
