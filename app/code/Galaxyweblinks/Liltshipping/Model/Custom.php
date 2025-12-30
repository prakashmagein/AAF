<?php
namespace Galaxyweblinks\Liltshipping\Model;

use Magento\Framework\Model\AbstractModel;

class Custom extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Galaxyweblinks\Liltshipping\Model\ResourceModel\Custom');
    }

    public function getCityAndSelectedFieldDetails()
    {
        $collection = $this->getCollection();
        $collection->addFieldToSelect('city');
        $collection->addFieldToSelect('selected');
        $collection->addFieldToSelect('transcity');

        return $collection->getData();
    }
}