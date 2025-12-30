<?php
/**
 * Copyright © Gwl All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Gwl\OrderDetails\Observers;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface as Logger;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Class SaveAddressAttributeObserver
 */
class SaveAddressAttributeObserver implements ObserverInterface
{
    protected $customerRepository;

    /**
     * @param Logger $logger
     */
    public function __construct(Logger $logger,AddressRepositoryInterface $addressRepository,  CustomerRepositoryInterface $customerRepository)
    {
        $this->logger = $logger;
        $this->addressRepository = $addressRepository;
        $this->customerRepository = $customerRepository;

    }

    /**
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $order = $observer->getEvent()->getOrder();
            $quote = $observer->getEvent()->getQuote();
            if ($quote->getBillingAddress()) {
                $order->getBillingAddress()->setDistrict(
                        $quote->getBillingAddress()->getExtensionAttributes()->getDistrict()
                    );

                $order->getBillingAddress()->setHouseDescription(
                        $quote->getBillingAddress()->getExtensionAttributes()->getHouseDescription()
                    );

                // $this->logger->info("frontend status name test-->".$quote->getBillingAddress()->getExtensionAttributes()->getCustomerMobile());

                // Save the customer mobile value to the telephone field in billing address
                $order->getBillingAddress()->setTelephone(
                        $quote->getBillingAddress()->getExtensionAttributes()->getCustomerMobile()
                    );

            }
            if (!$quote->isVirtual()) {
                $order->getShippingAddress()->setDistrict($quote->getShippingAddress()->getDistrict());

                $order->getShippingAddress()->setHouseDescription($quote->getShippingAddress()->getHouseDescription());

                // Save the customer mobile value to the telephone field in shipping address
                $order->getShippingAddress()->setTelephone(
                        $quote->getShippingAddress()->getExtensionAttributes()->getCustomerMobile()
                    );

                if($order->getShippingAddress()->getCustomerAddressId()){
                    $customerAddress = $this->addressRepository->getById($order->getShippingAddress()->getCustomerAddressId());
                    $customerAddress->setCustomAttribute('district', $quote->getShippingAddress()->getDistrict());
                    $customerAddress->setCustomAttribute('house_description', $quote->getShippingAddress()->getHouseDescription());

                    $customerAddress->setTelephone($quote->getShippingAddress()->getExtensionAttributes()->getCustomerMobile());

                    $this->addressRepository->save($customerAddress);
                }
            }

                $customerId = $order->getCustomerId();
                    if ($customerId) {
                        $customer = $this->customerRepository->getById($customerId);
                         $customAttributes = $customer->getCustomAttributes();
                         if (isset($customAttributes['customer_mobile'])) {
                            $customAttributeValue = $customAttributes['customer_mobile']->getValue();
                            $order->getShippingAddress()->setTelephone($customAttributeValue);
                            $order->getBillingAddress()->setTelephone($customAttributeValue);
                         }
                    }


            $order->save();
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
