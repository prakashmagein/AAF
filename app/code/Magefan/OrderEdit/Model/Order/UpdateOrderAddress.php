<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Model\Order;

use Magento\Sales\Model\Order;
use Magento\Quote\Model\Quote;

class UpdateOrderAddress extends AbstractUpdateOrder
{
    /**
     * @param Order $order
     * @param array $logOfChanges
     * @param Quote|null $quote
     * @param array|null $newAddress
     * @param string|null $typeOfAddress
     * @return bool
     */
    public function execute(Order $order, array &$logOfChanges, Quote $quote = null, array &$newAddress = null, string $typeOfAddress = null): bool
    {
        $address = null;
        $quoteAddress = null;

        if ($typeOfAddress === 'billing') {
            $address = $order->getBillingAddress();
            $quoteAddress = $quote->getBillingAddress();
        } elseif ($typeOfAddress === 'shipping') {
            $address = $order->getShippingAddress();
            $quoteAddress = $quote->getShippingAddress();
        }

        if ($address && $quoteAddress) {
            $allowedKeys = array_keys($address->getData());

            foreach ($newAddress as $newAddressFieldKey => $newAddressFieldValue) {
                $typedKey = (string)$newAddressFieldKey;
                $validValue = in_array($typedKey, $allowedKeys);

                if (!$validValue) {
                    continue;
                }

                if (is_array($newAddressFieldValue)) {
                    $newAddressFieldWithMultiLines = '';

                    foreach ($newAddressFieldValue as $newAddressFieldLine) {
                        $newAddressFieldWithMultiLines .= $newAddressFieldLine . PHP_EOL;
                    }

                    $newAddressFieldValue = trim($newAddressFieldWithMultiLines);
                }

                $typedValue =  (string)$newAddressFieldValue;
                $needToChange = (string)$address->getData($typedKey) !== $typedValue;

                if ($needToChange) {
                    $address->setData($typedKey, $typedValue);
                    $quoteAddress->setData($typedKey, $typedValue);
                    $this->writeChanges(self::SECTION_ADDRESS, $logOfChanges, $typedKey, $typedKey, (string)$address->getData($typedKey), $typedValue);
                }
            }
        }

        return true;
    }
}
