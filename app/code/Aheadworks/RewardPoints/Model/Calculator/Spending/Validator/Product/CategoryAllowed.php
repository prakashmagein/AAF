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

namespace Aheadworks\RewardPoints\Model\Calculator\Spending\Validator\Product;

use Aheadworks\RewardPoints\Model\CategoryAllowed as CategoryAllowedModel;
use Magento\Framework\Validator\AbstractValidator;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote\Item\AbstractItem as QuoteAbstractItem;

/**
 * Class CategoryAllowed
 *
 * @package Aheadworks\RewardPoints\Model\Calculator\Spending\Validator\Product
 */
class CategoryAllowed extends AbstractValidator
{
    /**
     * @var CategoryAllowedModel
     */
    private $categoryAllowed;

    /**
     * @param CategoryAllowedModel $categoryAllowed
     */
    public function __construct(
        CategoryAllowedModel $categoryAllowed
    ) {
        $this->categoryAllowed = $categoryAllowed;
    }

    /**
     * Returns true if and only if quote item entity meets the validation requirements
     *
     * @param CartItemInterface|QuoteAbstractItem $quoteItem
     * @return bool
     */
    public function isValid($quoteItem)
    {
        $this->_clearMessages();

        $categoryIds = $quoteItem->getProduct()->getCategoryIds();
        if (!$this->categoryAllowed->isAllowedCategoryForSpendPoints($categoryIds)) {
            $this->_addMessages([
                __('Category of quote item product isn\'t allowed for points applying')
            ]);
            return false;
        }

        return true;
    }
}
