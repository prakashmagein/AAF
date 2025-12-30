<?php
namespace Customization\CityData\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class City extends AbstractDb
{
    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function _construct()
    {
        $this->_init('eadesign_romcity', 'id');
       // $this->_init('saudiceramics_cities', 'id');
    }
}
