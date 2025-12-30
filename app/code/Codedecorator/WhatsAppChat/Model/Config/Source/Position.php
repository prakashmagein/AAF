<?php
namespace Codedecorator\WhatsAppChat\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Position
 * @package Codedecorator\WhatsAppChat\Model\Config\Source
 */
class Position implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'top-left', 'label' => __('Top-Left')],
            ['value' => 'top-right', 'label' => __('Top-Right')],
            ['value' => 'bottom-left', 'label' => __('Bottom-Left')],
            ['value' => 'bottom-right', 'label' => __('Bottom-Right')]
        ];
    }
}
