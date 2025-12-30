<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Venustheme
 * @package    Ves_Brand
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Lof\ProductShipping\Block\Adminhtml\Shipping;

/**
 * ProductShipping edit block
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize ProductShipping edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'lofshipping_id';
        $this->_blockGroup = 'Lof_ProductShipping';
        $this->_controller = 'adminhtml_shipping';

        parent::_construct();

        if ($this->_isAllowedAction('Lof_ProductShipping::shipping_save')) {
            $this->buttonList->update('save','label',__('Save Shipping'));
            $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                -100
            );
        } else {
            $this->buttonList->remove('save');
        }

        if ($this->_isAllowedAction('Lof_ProductShipping::shipping_delete')) {
            $this->buttonList->update('delete', 'label', __('Delete Shipping'));
        } else {
            $this->buttonList->remove('delete');
        }
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('Lof_Shipping')->getId()) {
            return __("Edit Shipping '%1'", $this->escapeHtml($this->_coreRegistry->registry('Lof_Shipping')->getName()));
        } else {
            return __('New Shipping');
        }
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

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('lofmpproductshipping/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }

    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'page_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'page_content');
                }
            };
        ";
        return parent::_prepareLayout();
    }

    /**
     * after to html
     *
     * @param string $html
     * @return mixed
     */
    protected function _afterToHtml($html)
    {
        return parent::_afterToHtml($html);
        //return parent::_afterToHtml($html . $this->_getJsInitScripts());
    }

    /**
     * get js init scripts
     *
     * @return string
     */
    protected function _getJsInitScripts()
    {
        return "
        <script>
            require([
                'jquery',
                'domReady!'
            ], function($){
                function isFloat(n){
                    return Number(n) === n && n % 1 !== 0;
                }
                function isInt(n){
                    return Number(n) === n && n % 1 === 0;
                }
                $( \".numberOrAsterisk\" ).keypress(function(event){
                    //console.log(event.key);
                    var k=event.key;
                    var validateValueFloat = isFloat($(this).val());
                    var validateValueInt = isInt($(this).val());
                    if((!validateValueInt || !validateValue) && k!=\"*\") event.preventDefault();
                });
            });
        </script> ";

    }
}
