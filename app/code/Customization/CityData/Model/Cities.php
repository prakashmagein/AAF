<?php
namespace Customization\CityData\Model;

class Cities extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    /**
     * @var ResourceModel\City\Collection
     */
    private $_collection;

    public function __construct(
        \Customization\CityData\Model\ResourceModel\City\Collection $collection
    ) {
        $this->_collection = $collection;
    }

 public function getSpecificOptions($ids, $withEmpty = true)
    {
        $items = $this->_collection->getItems();
        $cities[] = ['label' => __('Please Select City'), 'value' => ''];
        foreach($items as $item){
                $cities[] = ['label' => __($item->getCity()), 'value' => $item->getCity()];
        }
        return $cities;
    }
    /**
     * @inheritdoc
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        $items = $this->_collection->getItems();
        $cities[] = ['label' => __('Please Select City'), 'value' => ''];
        foreach($items as $item){
                $cities[] = ['label' => __($item->getCity()), 'value' => $item->getCity()];
        }
        return $cities;
    }
}
