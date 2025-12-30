<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_ProductShipping
 *
 * @copyright  Copyright (c) 2016 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\ProductShipping\Block\Adminhtml\Shipping\Upload;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('shipping_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Upload'));

        $this->addTab(
            'upload_shipping',
            [
            'label' => __('Upload Shipping'),
            'content' => $this->getLayout()->createBlock('Lof\ProductShipping\Block\Adminhtml\Shipping\Upload\Tab\Main')->toHtml()
            ]
            );
    }

}
