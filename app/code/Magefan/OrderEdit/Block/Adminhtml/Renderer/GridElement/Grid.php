<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Block\Adminhtml\Renderer\GridElement;

use Magefan\OrderEdit\Block\Adminhtml\Renderer\Extended;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var CollectionFactory
     */
    private $customerFactory;

    /**
     * @var \Magento\Framework\View\Element\BlockInterface
     */
    private $blockGrid;

    /**
     * @param Context           $context
     * @param Data              $backendHelper
     * @param CollectionFactory $customerFactory
     * @param array             $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        CollectionFactory $customerFactory,
        array $data = []
    ) {
        $this->customerFactory = $customerFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
         parent::_construct();
         $this->setId('customer_grid');
         $this->setDefaultSort('entity_id');
         $this->setUseAjax(true);
    }

    /**
     * @return Grid
     */
    protected function _prepareCollection(): Grid
    {
        $collection = $this->customerFactory->create();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return Grid
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'assigned_user_role',
            [
                'header_css_class' => 'data-grid-actions-cell',
                'header' => __('Assigned'),
                'type' => 'radio',
                'html_name' => 'roles[]',
                'align' => 'center',
                'index' => 'entity_id'
            ]
        );

        $this->addColumn(
            'entity_id',
            [
                'header' => __('Customer Id'),
                'sortable' => true,
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'firstname',
            [
                'header' => __('Firstname'),
                'index' => 'firstname'
            ]
        );

        $this->addColumn(
            'lastname',
            [
                'header' => __('Lastname'),
                'index' => 'lastname'
            ]
        );

        $this->addColumn(
            'middlename',
            [
                'header' => __('Middlename'),
                'index' => 'middlename'
            ]
        );

        $this->addColumn(
            'email',
            [
                'header' => __('Email'),
                'index' => 'email'
            ]
        );

        $this->addColumn(
            'group_id',
            [
                'column_css_class'=>'no-display',
                'header_css_class'=>'no-display',
                'index' => 'group_id'
            ]
        );

        $this->addColumn(
            'dob',
            [
                'column_css_class'=>'no-display',
                'header_css_class'=>'no-display',
                'index' => 'dob'
            ]
        );

        $this->addColumn(
            'gender',
            [
                'column_css_class'=>'no-display',
                'header_css_class'=>'no-display',
                'index' => 'gender'
            ]
        );

        $this->addColumn(
            'prefix',
            [
                'column_css_class'=>'no-display',
                'header_css_class'=>'no-display',
                'index' => 'prefix'
            ]
        );

        $this->addColumn(
            'suffix',
            [
                'column_css_class'=>'no-display',
                'header_css_class'=>'no-display',
                'index' => 'suffix'
            ]
        );

        $this->addColumn(
            'taxvat',
            [
                'column_css_class'=>'no-display',
                'header_css_class'=>'no-display',
                'index' => 'taxvat'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl(): string
    {
        return $this->getUrl('mforderedit/order/grid');
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback(): string
    {
        $js = "
            function (grid, event) {
                var trElement = Event.findElement(event, 'tr');
                require([
                 'jquery'
                  ],function($) {
                     $(trElement).children(':first').children(':first').prop('checked', true);
                  }
                 );
            }
        ";
        return $js;
    }

    /**
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                \Magefan\OrderEdit\Block\Adminhtml\Renderer\GridElement\Grid::class,
                'newsletter.grid'
            );
        }
        return  $this->blockGrid;
    }
}
