<?php
/**
 * Address
 *
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */
namespace Magepow\OnestepCheckout\Plugin\Customer;

use Magento\Customer\Api\Data\AddressInterface;

class Address
{
    /**
     * @param \Magento\Customer\Model\Address $subject
     * @param \Closure $proceed
     * @param \Magento\Customer\Api\Data\AddressInterface $address
     * @return mixed
     */
    public function aroundUpdateData(\Magento\Customer\Model\Address $subject, \Closure $proceed, AddressInterface $address)
    {
        $object = $proceed($address);

        $addressData = $address->__toArray();
        if (isset($addressData['should_ignore_validation'])) {
            $object->setShouldIgnoreValidation($addressData['should_ignore_validation']);
        }

        return $object;
    }
}
