<?php
namespace Customization\CityData\Block;

use Magento\Framework\View\Element\Template;

class City extends Template
{
    /**
     * @var \Customization\CityData\Model\Cities
     */
    private $_cities;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Customization\CityData\Model\Cities $cities,
        array $data = array()
    ) {
        $this->_storeManager = $context->getStoreManager();
        $this->_cities = $cities;
         parent::__construct($context, $data);
    }
    public function getAllCity()
    {
        return $this->_cities->getAllOptions();
    }
}
