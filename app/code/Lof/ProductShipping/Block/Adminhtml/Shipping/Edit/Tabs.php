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
namespace Lof\ProductShipping\Block\Adminhtml\Shipping\Edit;

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
        $this->setTitle(__('Information'));

        $this->addTab(
            'shipping_information',
            [
            'label' => __('Shipping Information'),
            'content' => $this->getLayout()->createBlock('Lof\ProductShipping\Block\Adminhtml\Shipping\Edit\Tab\Main')->toHtml()

            ]
            );

        $this->addTab(
            'condition',
            [
            'label' => __('Cart Conditions'),
            'content' => $this->getLayout()->createBlock('Lof\ProductShipping\Block\Adminhtml\Shipping\Edit\Tab\Condition')->toHtml()

            ]
            );

        $this->addTab(
            'products',
            [
                'label' => __('Products'),
                'url' => $this->getUrl('lofmpproductshipping/*/products', ['_current' => true]),
                'class' => 'ajax'
            ]
        );

    }

}
