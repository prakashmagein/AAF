<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Model\Order;

use Magento\Sales\Model\Order;
use Magento\Quote\Model\Quote;

class UpdateOrderPurchasedFrom extends AbstractUpdateOrder
{
    /**
     * @param Order $order
     * @param array $logOfChanges
     * @param Quote|null $quote
     * @param string|null $orderNewShippingMethod
     * @return bool
     */
    public function execute(Order $order, array &$logOfChanges, Quote $quote = null, int $orderNewPurchasedFrom = null): bool
    {
        try {
            $store = $this->storeManager->getStore($orderNewPurchasedFrom);
        } catch (NoSuchEntityException $e) {
            return false;
        }

        $websiteName = (string)$store->getWebsite()->getName();
        $storeName =   (string)$store->getFrontendName();
        $storeViewName = $store->getName();

        $orderCurrentStoreName = (string)$order->getStoreName();
        $orderNewStoreName = $websiteName . ' ' . $storeName . ' ' . $storeViewName;

        if ($orderCurrentStoreName !== $orderNewStoreName) {
            $this->writeChanges(
                self::SECTION_ORDER_INFO,
                $logOfChanges,
                'purchased_from',
                'Purchased From',
                $orderCurrentStoreName,
                $orderNewStoreName
            );

            $order->setStoreName($orderNewStoreName);
            $order->setStoreId($orderNewPurchasedFrom);
        }

        return true;
    }
}
