<?php
/**
 * Magedelight
 * Copyright (C) 2022 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_SMSProfile
 * @copyright Copyright (c) 2022 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\SMSProfile\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlFactory;
use Magento\Framework\Exception\CouldNotSaveException;

class OrderPlacebefore implements ObserverInterface
{
   
    private $messageManager;
    private $request;
    private $helper;
    private $urlModel;
    private $_responseFactory;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        UrlFactory $urlFactory,
        \Magedelight\SMSProfile\Helper\Data $helper
    ) {
        $this->request = $request;
        $this->messageManager = $messageManager;
        $this->helper = $helper;
        $this->urlModel = $urlFactory->create();
        $this->_responseFactory = $responseFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
         $post =  $this->request->getPost();
         $order = $observer->getOrder();
         $codOtp = 1;
         $address = $order->getShippingAddress();
        if ($address) {
            $tel = $address->getTelephone();
            $payment = $order->getPayment();
            $title = $payment->getMethod();
            $paymentAdditionalInformation = $payment->getAdditionalInformation();
            if (isset($paymentAdditionalInformation ['codotp'])) {
                $codOtp = $paymentAdditionalInformation ['codotp'];
            }
            if ($codOtp!= 1 && $this->helper->getOtpForCOD() && $this->helper->getModuleStatus() && $tel != '' && $title == 'cashondelivery') {
                   throw new CouldNotSaveException(__('Please verify OTP.'));
                  return false;
            } else {
                return $this;
            }
        }
    }
}
