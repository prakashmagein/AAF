<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_GoogleMapPinAddress
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\GoogleMapPinAddress\Plugin\Magento\Checkout\Model;
    
class GuestPaymentInformationManagement
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
        
    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $coreSession;

    /**
     * Constructor.
     *
     * @param \Psr\Log\LoggerInterface                             $logger
     * @param \Magento\Framework\Session\SessionManagerInterface   $coreSession
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Session\SessionManagerInterface $coreSession
    ) {
        $this->logger = $logger;
        $this->coreSession = $coreSession;
    }

    /**
     * Before save Payment Information And Place Order function
     *
     * @param \Magento\Checkout\Model\GuestPaymentInformationManagement $subject
     * @param string $cartId
     * @param string $email
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface $billingAddress
     * @return array
     */
    public function beforesavePaymentInformationAndPlaceOrder(
        \Magento\Checkout\Model\GuestPaymentInformationManagement $subject,
        $cartId,
        $email,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        if ($billingAddress) {
            $billLatLong = [
                'latitude' => $billingAddress->getExtensionAttributes()->getLatitude(),
                'longitude' => $billingAddress->getExtensionAttributes()->getLongitude()
            ];
            $this->coreSession->setBillingLatLong($billLatLong);
        }
        return [$cartId,$email,$paymentMethod,$billingAddress];
    }
}
