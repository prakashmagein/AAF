<?php
namespace Codedecorator\WhatsAppChat\Model\Config\Backend;

use Magento\Config\Model\Config\Backend\Image as CoreImage;

class Image extends CoreImage
{
    /**
     * Getter for allowed extensions of uploaded files
     *
     * @return string[]
     */
    protected function _getAllowedExtensions()
    {
        return ['jpg', 'jpeg', 'gif', 'png', 'svg'];
    }
}
