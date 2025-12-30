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
namespace Aheadworks\RewardPoints\Model\Calculator\Spending;

use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote\Item\AbstractItem as QuoteAbstractItem;
use Magento\Framework\Validator\ValidatorInterface;
use Magento\Bundle\Model\Product\Type as BundleProductType;

/**
 * Class Checker
 *
 * @package Aheadworks\RewardPoints\Model\Calculator\Spending
 */
class Checker
{
    /**
     * @var ValidatorInterface
     */
    private $quoteItemValidator;

    /**
     * @param ValidatorInterface $quoteItemValidator
     */
    public function __construct(
        ValidatorInterface $quoteItemValidator
    ) {
        $this->quoteItemValidator = $quoteItemValidator;
    }

    /**
     * Check if reward points can be spend on quote item
     *
     * @param CartItemInterface|QuoteAbstractItem $quoteItem
     * @return bool
     */
    public function canSpendRewardPointsOnQuoteItem($quoteItem)
    {
        try {
            $canSpend = $this->quoteItemValidator->isValid($quoteItem);
        } catch (\Exception $exception) {
            $canSpend = false;
        }
        return $canSpend;
    }

    /**
     * Check if reward points can be spent partly on children
     *
     * @param CartItemInterface|QuoteAbstractItem $quoteItem
     * @return bool
     */
    public function canSpendRewardPointsPartlyOnChildren(CartItemInterface|QuoteAbstractItem $quoteItem): bool
    {
        return $quoteItem->getProductType() == BundleProductType::TYPE_CODE;
    }
}
