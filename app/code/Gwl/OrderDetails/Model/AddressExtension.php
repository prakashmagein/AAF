<?php
namespace Gwl\OrderDetails\Model;

use Gwl\OrderDetails\Api\Data\AddressExtensionInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

class AddressExtension extends AbstractExtensibleObject implements AddressExtensionInterface
{
    private $companyCheck;
    
    /**
     * {@inheritdoc}
     */
    public function getCustomerMobile()
    {
        return $this->_get('customer_mobile');
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerMobile($customerMobile)
    {
        return $this->setData('customer_mobile', $customerMobile);
    }


     /**
     * Set the value of companyCheck
     *
     * @param mixed $companyCheck
     */
    public function setCompanyCheck($companyCheck)
    {
        $this->companyCheck = $companyCheck;
    }

    /**
     * Get the value of companyCheck
     *
     * @return mixed
     */
    public function getCompanyCheck()
    {
        return $this->companyCheck;
    }

}