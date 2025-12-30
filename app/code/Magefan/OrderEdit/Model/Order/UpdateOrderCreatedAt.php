<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Model\Order;

use Magento\Sales\Model\Order;
use Magento\Quote\Model\Quote;

class UpdateOrderCreatedAt extends AbstractUpdateOrder
{
    /**
     * @param Order $order
     * @param array $logOfChanges
     * @param Quote|null $quote
     * @param string|null $orderNewShippingMethod
     * @return bool
     */
    public function execute(Order $order, array &$logOfChanges, Quote $quote = null, string $orderNewDate = null): bool
    {
        if (!$this->isValidDate($orderNewDate)) {
            return false;
        }

        $timezone = (string)$this->storeManager->getStore()->getConfig('general/locale/timezone');
        $orderCurrentDate = (string)$order->getCreatedAt();
        $orderCurrentDateGmt = $this->convertFromOneToAnotherTimezone('GMT', $timezone, $orderCurrentDate);

        if ($orderCurrentDateGmt !== $orderNewDate) {
            $this->writeChanges(self::SECTION_ORDER_INFO, $logOfChanges, 'order_date', 'Order Date', $orderCurrentDate, $orderNewDate);
            $gmtValue = $this->convertFromOneToAnotherTimezone($timezone, 'GMT', $orderNewDate);
            $order->setCreatedAt($gmtValue);
        }
        return true;
    }

    /**
     * @param  string $date
     * @return bool
     */
    public function isValidDate(string $date): bool
    {
        $formatOne = 'Y-m-d';
        $formatTwo = 'Y-m-d H:i:s';

        $dOne = \DateTime::createFromFormat($formatOne, $date);
        $dTwo = \DateTime::createFromFormat($formatTwo, $date);

        $isValidDateOne = $dOne && ($dOne->format($formatOne) === $date);
        $isValidDateTwo = $dTwo && ($dTwo->format($formatTwo) === $date);

        if (!($isValidDateOne || $isValidDateTwo)) {
            return false;
        }

        return true;
    }

    /**
     * @param  string $firstTimeZone
     * @param  string $secondTimeZone
     * @param  string $date
     * @return string
     */
    public function convertFromOneToAnotherTimezone(string $firstTimeZone, string $secondTimeZone, string $date): string
    {
        $firstDate = date_create($date, timezone_open($firstTimeZone));
        $secondDate = date_timezone_set($firstDate, timezone_open($secondTimeZone));
        $resultDate = $secondDate->format('Y-m-d H:i:s');

        return $resultDate;
    }
}
