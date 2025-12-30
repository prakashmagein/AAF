<?php
 /**
  * Magedelight
  * Copyright (C) 2022 Magedelight <info@magedelight.com>
  *
  * @category  Magedelight
  * @package   Magedelight_SMSProfile
  * @copyright Copyright (c) 2022 Mage Delight (http://www.magedelight.com/)
  * @license   http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
  * @author    Magedelight <info@magedelight.com>
  */

namespace Magedelight\SMSProfile\Plugin;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollection ;
use Magento\Store\Model\StoreManagerInterface;

class CustomerEmailValidatePlugin
{

    private $customerCollection;
    private $storeManager;
    public function __construct(
        StoreManagerInterface $storeManager,
        CustomerCollection $customerCollection
    ) {
        $this->customerCollection = $customerCollection;
        $this->storeManager = $storeManager;
    }
    public function beforeIsEmailAvailable(\Magento\Customer\Model\AccountManagement $subject, $customerEmail)
    {
        if (is_numeric($customerEmail)) {
            $customerCollections = $this->getCustomerByPhone($customerEmail);
            foreach ($customerCollections as $customer) {
                $customerEmail = $customer->getEmail();
            }
        }
        return $customerEmail;
    }

    public function getCustomerByPhone($phone)
    {
        $customerCollection = $this->customerCollection->create();
        $customerCollection->addAttributeToSelect('*')
                           ->addAttributeToFilter('customer_mobile', $phone)
                           ->load();
        return $customerCollection;
    }
}
