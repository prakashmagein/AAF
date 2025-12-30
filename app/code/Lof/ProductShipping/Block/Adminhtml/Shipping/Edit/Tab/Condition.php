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

class Condition extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface
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

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Cart Conditions')]);

        $fieldset->addField(
            'dest_country_id',
            'select',
            [
                'name' => 'dest_country_id',
                'label' => __('Country code'),
                'title' => __('Country code'),
                'required' => true,
                'values' => $this->country->toOptionArray(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'dest_region_id',
            'text',
            [
                'name' => 'dest_region_id',
                'label' => __('Region code'),
                'title' => __('Region code'),
                //'required' => true,
                'after_element_html' => __("Enter specific region code or enter * for all"),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'dest_zip',
            'text',
            [
                'name' => 'dest_zip',
                'label' => __('Zip from'),
                'title' => __('Zip from'),
                'required' => true,
                'class' => 'numberOrAsterisk',
                //'class' => 'validate-number',
                'after_element_html' => __("Enter specific zip or * for all"),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'dest_zip_to',
            'text',
            [
                'name' => 'dest_zip_to',
                'label' => __('Zip to'),
                'title' => __('Zip to'),
                'after_element_html' => __("Enter specific zip or * for all"),
                'required' => true,
                'class' => 'numberOrAsterisk',
                //'class' => 'validate-number',
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'weight_from',
            'text',
            [
                'name' => 'weight_from',
                'label' => __('Weight from'),
                'title' => __('Weight from'),
                'required' => true,
                //'class' => 'validate-number',
                'class' => 'numberOrAsterisk',
                'after_element_html' => __("Enter specific weight Interval Number (ex: 0) or * to accept any weight"),
                'disabled' => $isElementDisabled
            ]
        );
        //$model->setData("weight_from","*");

        $fieldset->addField(
            'weight_to',
            'text',
            [
                'name' => 'weight_to',
                'label' => __('Weight to'),
                'title' => __('Weight to'),
                'required' => true,
                //'class' => 'validate-number',
                'class' => 'numberOrAsterisk',
                'after_element_html' => __("Enter specific weight Interval Number (ex: 10) or * to accept any weight"),
                'disabled' => $isElementDisabled
            ]
        );
        //$model->setData("weight_to","*");

        $fieldset->addField(
            'quantity_from',
            'text',
            [
                'name' => 'quantity_from',
                'label' => __('Quantity from'),
                'title' => __('Quantity from'),
                'required' => true,
                'class' => 'numberOrAsterisk',
                //'class' => 'validate-number',
                'after_element_html' => __("Enter specific quantity or * to accept any number of quantity"),
                'disabled' => $isElementDisabled
            ]
        );
        //$model->setData("quantity_from","*");

        $fieldset->addField(
            'quantity_to',
            'text',
            [
                'name' => 'quantity_to',
                'label' => __('Quantity to'),
                'title' => __('Quantity to'),
                'required' => true,
                'class' => 'numberOrAsterisk',
                //'class' => 'validate-number',
                'after_element_html' => __("Enter specific quantity or * to accept any number of quantity"),
                'disabled' => $isElementDisabled
            ]
        );

        if (!$model->getId()) {
            $model->setData("quantity_from","*");
            $model->setData("quantity_to","*");
            $model->setData("weight_from","*");
            $model->setData("weight_to","*");
            $model->setData("dest_region_id","*");
            $model->setData("dest_zip","*");
            $model->setData("dest_zip_to","*");
        }

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
