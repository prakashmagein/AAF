<?php
namespace Galaxyweblinks\Liltshipping\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Galaxyweblinks\Liltshipping\Model\CustomFactory;

class City implements ArrayInterface
{
    protected $customFactory;

    public function __construct(CustomFactory $customFactory)
    {
        $this->customFactory = $customFactory;
    }

    public function toOptionArray()
    {
        
        $custom = $this->customFactory->create();
        $data = $custom->getCityAndSelectedFieldDetails();

        $responses=[];

            foreach ($data as $item) {
                $city = $item['city'];
                $selected = $item['selected'];
               
                $response = [
                    'value' => $city,
                    'label' => $city,
                ];

                array_push($responses, $response);
                
            }

        return $responses;
    }

    
}