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
 * @copyright  Copyright (c) 2022 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\ProductShipping\Block\Adminhtml\Shipping\Edit\Tab;

use \Magento\Backend\Block\Widget\Tab\TabInterface;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    /**
     * @var \Lof\ProductShipping\Model\Config\Source\Country
     */
    protected $country;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Lof\ProductShipping\Model\Config\Source\Country $country,
        array $data = []
    )
    {
        $this->country = $country;
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('lofproductshipping_shipping');

        if ($this->_isAllowedAction('Lof_ProductShipping::shipping')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Shipping Information')]);

        if ($model->getId()) {
            $fieldset->addField('lofshipping_id', 'hidden', ['name' => 'lofshipping_id']);
        }
        $fieldset->addField(
            'shipping_method',
            'text',
            [
                'name' => 'shipping_method',
                'label' => __('Shipping Type'),
                'title' => __('Shipping Type'),
                //'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        $form->getElement(
            'shipping_method'
        )->setRenderer(
            $this->getLayout()->createBlock('Lof\ProductShipping\Block\Adminhtml\Shipping\Renderer\ShippingTab')
        );

        $fieldset->addField(
            'description',
            'text',
            [
                'name' => 'description',
                'label' => __('Shipping Rate Description'),
                'title' => __('Shipping Rate Description'),
                //'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'priority',
            'text',
            [
                'name' => 'priority',
                'label' => __('Priority'),
                'title' => __('Priority'),
                'required' => true,
                'class' => 'validate-number',
                'after_element_html' => __("<br/>Enter priority use for sorting get rate, value 0 is the highest priority. Module will get first rate if match more rates"),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'price_for_unit',
            'select',
            [
                'label'    => __('Use rate price for each unit'),
                'title'    => __('Use rate price for each unit'),
                'name'     => 'price_for_unit',
                'style'    => 'width: 15rem;',
                'after_element_html'    => __('<br/>Select Yes to apply price for each unit, No to apply for total. <br/>Example <br/><strong>Yes</strong>: in shopping cart have 2 matched items, it will calculate rate price = rate_price * qty.<br/><strong>No</strong>: will apply rate_price, and will not apply second_price.'),
                'options'  => $model->getYesNo(),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'price',
            'text',
            [
                'name' => 'price',
                'label' => __('Shipping Price'),
                'title' => __('Shipping Price'),
                'required' => true,
                'class' => 'validate-number',
                'after_element_html' => __("Enter shipping price"),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'allow_second_price',
            'select',
            [
                'label'    => __('Allow use Second Price'),
                'title'    => __('Status'),
                'name'     => 'allow_second_price',
                'style'    => 'width: 15rem;',
                'after_element_html'    => __('<br/>Enable to allow use Second Price for the rate, disable will not use.'),
                'options'  => $model->getYesNo(),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'second_price',
            'text',
            [
                'name' => 'second_price',
                'label' => __('Second Shipping Price'),
                'title' => __('Second Shipping Price'),
                'required' => false,
                'after_element_html' => __("Enter shipping second price. Shipping price apply from second product in cart. Default: 0"),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'allow_free_shipping',
            'select',
            [
                'label'    => __('Allow use Fee Shipping Check'),
                'title'    => __('Status'),
                'name'     => 'allow_free_shipping',
                'style'    => 'width: 15rem;',
                'after_element_html'    => __('<br/>Enable to allow check is free shipping when cart subtotal >= <strong>Min amount Cart total for Free Shipping</strong>.'),
                'options'  => $model->getYesNo(),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'free_shipping',
            'text',
            [
            'name' => 'free_shipping',
            'label' => __('Min amount Cart total for Free Shipping'),
            'title' => __('Min amount Cart total for Free Shipping'),
            'required' => false,
            'after_element_html'=> __('Minimun order amount for free shipping. Default: 0'),
            'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'cost',
            'text',
            [
                'name' => 'cost',
                'label' => __('Shipping Cost'),
                'title' => __('Shipping Cost'),
                'required' => false,
                'after_element_html' => __("Enter shipping cost. Default: 0"),
                'disabled' => $isElementDisabled
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Shipping Data');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Shipping Data');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
