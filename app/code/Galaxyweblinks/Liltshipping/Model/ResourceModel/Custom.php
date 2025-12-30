<?php
namespace Galaxyweblinks\Liltshipping\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Custom extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('lilt_shipping_city_table', 'id');
    }
}