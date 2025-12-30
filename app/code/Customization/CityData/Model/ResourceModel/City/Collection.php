<?php
namespace Customization\CityData\Model\ResourceModel\City;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Customization\CityData\Model\ResourceModel\City;

class Collection extends AbstractCollection
{
    /**
     * @type string
     */
    protected $_idFieldName = 'id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Customization\CityData\Model\City::class, City::class);
    }

}
