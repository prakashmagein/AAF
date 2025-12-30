<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Block\Adminhtml\Order;

class Grid extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @return \Magento\Backend\Block\Widget\Form\Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'order_edit_form',
                    'action' => 'action',
                    'method' => 'post',
                    'enctype' => 'multipart/form-data',
                ]
            ]
        );

        $form->setHtmlIdPrefix('order_edit_');

        $fieldsetGrid = $form->addFieldset(
            'base_fieldset_grid',
            ['label' => __('General Information'), 'class' => 'fieldset-wide']
        );

        $fieldsetGrid->addType(
            'base_field_gridd',
            \Magefan\OrderEdit\Block\Adminhtml\Renderer\GridElement::class
        );

        $fieldsetGrid->addField(
            'base_field_grid',
            'base_field_gridd',
            [
                'name' => 'base_field_grid',
                'label' => __('Please select a customer'),
                'title' => __('Please select a customer')
            ]
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
