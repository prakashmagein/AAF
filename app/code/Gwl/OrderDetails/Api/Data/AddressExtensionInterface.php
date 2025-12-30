<?php
namespace Gwl\OrderDetails\Api\Data;

use Magento\Framework\Api\ExtensionAttributesInterface;

interface AddressExtensionInterface extends ExtensionAttributesInterface
{
    /**
     * Get customer mobile.
     *
     * @return string|null
     */
    public function getCustomerMobile();

    /**
     * Set customer mobile.
     *
     * @param string $customerMobile
     * @return $this
     */
    public function setCustomerMobile($customerMobile);
}