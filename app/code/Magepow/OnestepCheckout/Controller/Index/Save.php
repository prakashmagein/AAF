<?php

namespace Magepow\OnestepCheckout\Controller\Index;

use Magento\Checkout\Model\Session;

class Save extends \Magento\Framework\App\Action\Action
{
        
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        Session $checkoutSession
    ) {
    parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $this->checkoutSession->setOnestepCheckoutData($data);
    }
}