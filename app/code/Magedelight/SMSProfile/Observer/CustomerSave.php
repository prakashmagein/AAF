<?php
/**
 * Magedelight
 * Copyright (C) 2016 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_SMSProfile
 * @copyright Copyright (c) 2016 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\SMSProfile\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlFactory;

class CustomerSave implements ObserverInterface
{
   
    private $messageManager;

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
         
        if ($post['signupOtpValidation'] != 1 && $this->helper->getModuleStatus() && $post['customer_mobile'] != '' && $this->helper->getSmsProfilePhoneRequiredOnSignUp()) {
            $message  =__('Please verify OTP.');
            $this->messageManager->addError($message);
            $url = $this->urlModel->getUrl('*/*/create', ['_secure' => true]);
            $this->_responseFactory->create()->setRedirect($url)->sendResponse();
            $this->messageManager->addError($message);
            //die;
        } else {
            return $this;
        }
    }
}
