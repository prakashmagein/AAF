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

use Aheadworks\RewardPoints\Model\Config;
use Aheadworks\RewardPoints\Model\Quote\Item\Checker as QuoteItemChecker;
use Magento\Framework\Validator\AbstractValidator;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote\Item\AbstractItem as QuoteAbstractItem;
use Magento\Store\Model\StoreManagerInterface;

class Subscription extends AbstractValidator
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var QuoteItemChecker
     */
    private $quoteItemChecker;

    /**
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param QuoteItemChecker $quoteItemChecker
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        QuoteItemChecker $quoteItemChecker
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->quoteItemChecker = $quoteItemChecker;
    }

    /**
     * Returns true if and only if quote item entity meets the validation requirements
     *
     * @param CartItemInterface|QuoteAbstractItem $quoteItem
     * @return bool
     */
    public function isValid($quoteItem)
    {
        if ($this->quoteItemChecker->hasSubscriptionProduct($quoteItem)
            && !$this->canSpendRewardPointsOnSubscriptionProduct($quoteItem)
        ) {
            $this->_addMessages([
                __('Applying points on subscription products are disabled')
            ]);
            return false;
        }

        return true;
    }

    /**
     * Check if reward points can be spend on subscription product
     *
     * @param CartItemInterface|QuoteAbstractItem $quoteItem
     * @return bool
     */
    private function canSpendRewardPointsOnSubscriptionProduct($quoteItem)
    {
        try {
            $websiteId = $this->storeManager->getStore($quoteItem->getStoreId())->getWebsiteId();
            return $this->config->isEnableApplyingPointsOnSubscription($websiteId);
        } catch (\Exception $exception) {
            return false;
        }
    }
}
