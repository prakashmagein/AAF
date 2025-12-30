<?php
/**
 * ShippingMethodManagement
 *
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */
namespace Magepow\OnestepCheckout\Plugin\Checkout;

use Closure;
use Exception;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\EstimateAddressInterface;
use Magento\Quote\Model\ShippingMethodManagement as QuoteShippingMethodManagement;
use Magento\Customer\Api\Data\AddressInterface as CustomerAddressInterface;
use Magento\Quote\Model\Quote;

class ShippingMethodManagement
{
    /**
     * Quote repository.
     *
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * Customer Address repository
     *
     * @var AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * @param CartRepositoryInterface    $quoteRepository
     * @param AddressRepositoryInterface $addressRepository
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        AddressRepositoryInterface $addressRepository
    ) {
        $this->quoteRepository   = $quoteRepository;
        $this->addressRepository = $addressRepository;
    }

    /**
     * @param QuoteShippingMethodManagement $subject
     * @param Closure                       $proceed
     * @param                               $cartId
     * @param EstimateAddressInterface      $address
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function aroundEstimateByAddress(
        QuoteShippingMethodManagement $subject,
        Closure $proceed,
        $cartId,
        EstimateAddressInterface $address
    ) {
        $this->saveAddress($cartId, $address);

        return $proceed($cartId, $address);
    }

    /**
     * @param QuoteShippingMethodManagement $subject
     * @param Closure                       $proceed
     * @param                               $cartId
     * @param AddressInterface              $address
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function aroundEstimateByExtendedAddress(
        QuoteShippingMethodManagement $subject,
        Closure $proceed,
        $cartId,
        AddressInterface $address
    ) {
        foreach ($address->getCustomAttributes() as $attribute) {
            if (isset($attribute->getValue()['value'])) {
                $attribute->setValue($attribute->getValue()['value']);
            }
        }

        $this->saveAddress($cartId, $address);

        return $proceed($cartId, $address);
    }

    /**
     * @param QuoteShippingMethodManagement $subject
     * @param Closure                       $proceed
     * @param                               $cartId
     * @param                               $addressId
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function aroundEstimateByAddressId(
        QuoteShippingMethodManagement $subject,
        Closure $proceed,
        $cartId,
        $addressId
    ) {
        $address = $this->addressRepository->getById($addressId);
        $this->saveAddress($cartId, $address);

        return $proceed($cartId, $addressId);
    }

    /**
     * @param                                                                    $cartId
     * @param EstimateAddressInterface|AddressInterface|CustomerAddressInterface $address
     *
     * @return $this
     * @throws NoSuchEntityException
     */
    private function saveAddress($cartId, $address)
    {
        /** @var Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);

        if (!$quote->isVirtual()) {
            $region = $this->checkRegion((array) $address->getRegion());
            $addressData = [
                AddressInterface::KEY_COUNTRY_ID        => $address->getCountryId(),
                AddressInterface::KEY_FIRSTNAME         => $address->getFirstname(),
                AddressInterface::KEY_LASTNAME          => $address->getLastname(),
                AddressInterface::KEY_TELEPHONE         => $address->getTelephone(),
                AddressInterface::KEY_COMPANY           => $address->getCompany(),
                AddressInterface::KEY_POSTCODE          => $address->getPostcode(),
                AddressInterface::KEY_STREET            => $address->getStreet(),
                AddressInterface::KEY_CITY              => $address->getCity(),
                AddressInterface::CUSTOMER_ADDRESS_ID   => $address->getId(),
                AddressInterface::KEY_REGION_ID         => $address->getRegionId(),
                AddressInterface::KEY_REGION            => $region 
            ];

            $shippingAddress = $quote->getShippingAddress();
            try {
                $shippingAddress->addData($addressData)
                    ->save();
                $this->quoteRepository->save($quote);
            } catch (Exception $e) {
                return $this;
            }
        }

        return $this;
    }

    /**
     * Check Region New Address
     */

    public function checkRegion($region){
        if(count($region) == 1) {
           return $region[0];
        }elseif (count($region) > 1) {
            return $region;
        }else{
            return '';
        }
    }

}
