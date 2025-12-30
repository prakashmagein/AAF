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

namespace Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver;

use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemInterface;
use Aheadworks\RewardPoints\Model\Config;
use Aheadworks\RewardPoints\Model\Validator\Product\Price\Discount\Checker as DiscountChecker;
use Magento\Framework\Exception\ConfigurationMismatchException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\CreditmemoItemInterface;

/**
 * Filter to exclude items with discount
 */
class ItemFilter
{
    /**
     * @param Config $config
     * @param DiscountChecker $discountChecker
     */
    public function __construct(
        private readonly Config $config,
        private readonly DiscountChecker $discountChecker
    ) {
    }

    /**
     * Filter items without discount
     *
     * @param CreditmemoItemInterface[]|InvoiceInterface[]|CartItemInterface[]|EarnItemInterface[] $items
     * @return array
     * @throws ConfigurationMismatchException
     * @throws NoSuchEntityException
     */
    public function filterItemsWithoutDiscount(array $items): array
    {
        if (!$this->config->isRestrictEarningPointsOnSale()) {
            return $items;
        }
        $resultItems = $items;
        $itemIdsToRemove = [];
        foreach ($items as $item) {
            $productId = $item->getProductId();
            if ($this->discountChecker->checkHasDiscountByProductId((int)$productId)) {
                $itemIdsToRemove[] = $item->getId();
            }
        }
        if (count($itemIdsToRemove)) {
            foreach ($items as $key => $value) {
                if (in_array($value->getParentItemId(), $itemIdsToRemove)
                    || in_array($value->getId(), $itemIdsToRemove)
                ) {
                    unset($resultItems[$key]);
                }
            }
        }

        return $resultItems;
    }
}
