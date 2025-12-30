<?php
namespace Galaxyweblinks\Liltshipping\Model\ResourceModel\Custom;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Galaxyweblinks\Liltshipping\Model\Custom', 'Galaxyweblinks\Liltshipping\Model\ResourceModel\Custom');
    }
}