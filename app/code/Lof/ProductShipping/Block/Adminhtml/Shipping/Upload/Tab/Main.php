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

namespace Lof\ProductShipping\Block\Adminhtml\Shipping\Upload\Tab;

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
        array $data = []
    ) {
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
        $form = $this->_formFactory->create(
            ['data' =>
                [
                    'id' => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data',
                ]
            ]
        );
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('CSV File'),
                'class' => 'fieldset-wide'
            ]
        );
        $fieldset->addField(
            'title',
            'file',
            [
                'label'     => __('Upload CSV'),
                'class'     => 'required-entry',
                'required'  => true,
                'name'      => 'import_file'
            ]
        );

        $fieldset->addField(
            'sample_file',
            'link',
            [
                'href' => $this->getViewFileUrl('Lof_ProductShipping::sampleProductShipping.csv'),
                'value'  => 'Download Sample File (sampleProductShipping.csv)'
            ]
        );

        $fieldset->addField(
            'sample_file2',
            'link',
            [
                'href' => $this->getViewFileUrl('Lof_ProductShipping::sampleProductShippingNotForUnit.csv'),
                'value'  => 'Download Sample File 2 (sampleProductShippingNotForUnit.csv)'
            ]
        );

        $fieldset->addField(
            'sample_file3',
            'link',
            [
                'href' => $this->getViewFileUrl('Lof_ProductShipping::sampleProductShippingSameName.csv'),
                'value'  => 'Download Sample File 3 (sampleProductShippingSameName.csv)'
            ]
        );

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
