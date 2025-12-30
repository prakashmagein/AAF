<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Block\Adminhtml\Order;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{

    /**
     * Initialize cms page edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Magefan_OrderEdit';
        $this->_controller = 'adminhtml_order';

        parent::_construct();

        if ($this->_isAllowedAction('Magento_Sales::actions_edit')) {
            $this->buttonList->add(
                'Save',
                [
                    'label' => __('Save'),
                    'class' => 'action-default primary add',
                    'data_attribute' => [
                        'role' => 'template-save',
                    ]
                ],
                -100
            );
            $this->buttonList->add(
                'Back',
                [
                    'label' => __('Back'),
                    'class' => 'back',
                    'data_attribute' => [
                        'role' => 'template-back',
                    ]
                ],
                -110
            );

        }

        $this->buttonList->remove('back');
        $this->buttonList->remove('delete');
        $this->buttonList->remove('save');
    }

    /**
     * Check permission for passed action
     *
     * @param  string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId): bool
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
