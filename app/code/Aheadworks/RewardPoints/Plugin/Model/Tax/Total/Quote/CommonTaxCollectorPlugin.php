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

namespace Aheadworks\RewardPoints\Plugin\Model\Tax\Total\Quote;

use Aheadworks\RewardPoints\Model\Config;
use Magento\Quote\Model\Quote\Item\AbstractItem as QuoteItem;
use Magento\Tax\Api\Data\QuoteDetailsItemInterface;
use Magento\Tax\Api\Data\QuoteDetailsItemInterfaceFactory;
use Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector;

class CommonTaxCollectorPlugin
{
    /**
     * @param Config $config
     */
    public function __construct(
        private readonly Config $config
    ) {
    }

    /**
     * Update discount amount value
     *
     * @param CommonTaxCollector $commonTaxCollector
     * @param QuoteDetailsItemInterface $quoteDetailsItem
     * @param QuoteDetailsItemInterfaceFactory $quoteDetailsItemFactory
     * @param QuoteItem $quoteItem
     * @param bool $priceIncludesTax
     * @param bool $useBaseCurrency
     * @return QuoteDetailsItemInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterMapItem(
        CommonTaxCollector $commonTaxCollector,
        QuoteDetailsItemInterface $quoteDetailsItem,
        QuoteDetailsItemInterfaceFactory $quoteDetailsItemFactory,
        QuoteItem $quoteItem,
        $priceIncludesTax,
        $useBaseCurrency
    ): QuoteDetailsItemInterface {
        $websiteId = (int) $quoteItem->getStore()->getWebsiteId();
        if (!$this->config->isApplyingPointsToTax($websiteId)) {
            $rewardPointsAmount = $useBaseCurrency ?
                $quoteItem->getBaseAwRewardPointsAmount() :
                $quoteItem->getAwRewardPointsAmount();

            $quoteDetailsItem->setDiscountAmount(
                $quoteDetailsItem->getDiscountAmount() + $rewardPointsAmount
            );
        }

        return $quoteDetailsItem;
    }
}
