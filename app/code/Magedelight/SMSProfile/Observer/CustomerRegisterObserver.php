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
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Event\Observer;

class CustomerRegisterObserver implements ObserverInterface
{
   
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;
    protected $request;

    function __construct(CustomerFactory $customerFactory, \Magento\Framework\App\RequestInterface $request)
    {
        $this->_customerFactory = $customerFactory;
        $this->request = $request;
    }

    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();
        $customerData = $event->getCustomer();

        if ($this->request->getPostValue('countryreg')) {
            $customer = $this->_customerFactory->create()->load($customerData->getId());
            $customer->setData('countryreg', $this->request->getPostValue('countryreg'));
            $customer->save();
        }
    }
}
