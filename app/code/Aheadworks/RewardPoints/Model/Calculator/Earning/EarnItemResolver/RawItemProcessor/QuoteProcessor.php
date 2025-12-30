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
namespace Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\RawItemProcessor;

use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ItemFilter;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item as QuoteItem;

/**
 * Class QuoteProcessor
 */
class QuoteProcessor
{
    /**
     * @var ItemGroupConverterInterface
     */
    private $itemGroupConverter;

    /**
     * @var ItemFilter
     */
    private $itemFilter;

    /**
     * @param ItemGroupConverterInterface $itemGroupConverter
     * @param ItemFilter $discountChecker
     */
    public function __construct(
        ItemGroupConverterInterface $itemGroupConverter,
        ItemFilter $itemFilter
    ) {
        $this->itemGroupConverter = $itemGroupConverter;
        $this->itemFilter = $itemFilter;
    }

    /**
     * @param Quote $quote
     * @return array [ItemInterface[], ...]
     */
    public function getItemGroups($quote)
    {
        /** @var QuoteItem[] $quoteItems */
        $quoteItems = $this->itemFilter->filterItemsWithoutDiscount($quote->getAllItems());
        $quoteItemGroups = $this->getQuoteItemsGrouped($quoteItems);
        $itemGroups = $this->itemGroupConverter->convert($quoteItemGroups);

        return $itemGroups;
    }

    /**
     * Get quote items grouped
     *
     * @param QuoteItem[] $quoteItems
     * @return array
     */
    private function getQuoteItemsGrouped($quoteItems)
    {
        $quoteGroups = [];
        /** @var QuoteItem $quoteItem */
        foreach ($quoteItems as $quoteItem) {
            $parentItemId = $quoteItem->getParentItemId();
            if ($parentItemId == null) {
                $parentItemId = $quoteItem->getItemId();
            }
            $quoteItem->setIsChildrenCalculated($quoteItem->isChildrenCalculated());
            $quoteGroups[$parentItemId][$quoteItem->getItemId()] = $quoteItem;
        }
        return $quoteGroups;
    }
}
