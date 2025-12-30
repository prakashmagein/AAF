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
use Magedelight\SMSProfile\Helper\Data as HelperData;

class CustomerSaveAfter implements ObserverInterface
{
   
    private $messageManager;
    /**  @var HelperData */
    private $datahelper;
    /**  @var \Magento\Customer\Model\Customer */
    private $customer;

    public function __construct(
        HelperData $dataHelper,
        \Magento\Customer\Model\Customer $customer
    ) {
        $this->datahelper = $dataHelper;
        $this->customer = $customer;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $savedCustomer = $observer->getData('customer_data_object');
        if ($this->datahelper->getModuleStatus() && $this->datahelper->getSmsProfileEmailOptionalOnSignUp()) {
            $storeDomain = $this->datahelper->getStoreDomain();
            if (strpos($savedCustomer->getEmail(), $storeDomain) !== false) {
                if (substr($savedCustomer->getEmail(), 0, 1)=="+") {
                    $customer = $this->customer->load($savedCustomer->getId());
                    $customer->setConfirmation(null);
                    $customer->save();
                }
            }
        }
    }
}
