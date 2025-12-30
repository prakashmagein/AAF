<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Plugin\Button;

use \Magefan\GuestToCustomer\Controller\Adminhtml\Order\Convert as ConvertController;
use \Magento\Backend\Block\Widget\Button\Toolbar\Interceptor;
use \Magento\Framework\View\Element\AbstractBlock;
use \Magento\Backend\Block\Widget\Button\ButtonList;

/**
 * Class ToolbarPlugin add Guest To Customer button for Toolbar.
 */
class ToolbarPlugin
{
    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $authorization;

    /**
     * ToolbarPlugin constructor.
     *
     * @param \Magento\Framework\AuthorizationInterface $authorization
     */
    public function __construct(
        \Magento\Framework\AuthorizationInterface $authorization
    ) {
        $this->authorization = $authorization;
    }

    /**
     * @param \Magento\Backend\Block\Widget\Button\Toolbar\Interceptor $subject
     * @param \Magento\Framework\View\Element\AbstractBlock            $context
     * @param \Magento\Backend\Block\Widget\Button\ButtonList          $buttonList
     */
    public function beforePushButtons(
        Interceptor $subject,
        AbstractBlock $context,
        ButtonList $buttonList
    ) {
        $order = false;
        $nameInLayout = $context->getNameInLayout();

        if ('sales_order_edit' == $nameInLayout) {
            $order = $context->getOrder();
        } elseif ('sales_invoice_view' == $nameInLayout) {
            $order = $context->getInvoice()->getOrder();
        } elseif ('sales_shipment_view' == $nameInLayout) {
            $order = $context->getShipment()->getOrder();
        } elseif ('sales_creditmemo_view' == $nameInLayout) {
            $order = $context->getCreditmemo()->getOrder();
        }

        if ($this->isAllowed()) {
            if ($order) {
                $buttonUrl = $context->getUrl(
                    'mforderedit/order/edit',
                    [
                    'order_id' => $order->getId(),
                    'form_type' => \Magefan\OrderEdit\Block\Adminhtml\Order\Edit\Form::ALL_TYPES_EDIT_FORM
                    ]
                );
                $buttonList->add(
                    'quick_edit',
                    ['label' => __('Quick Edit'),
                        'onclick' => 'window.location=\'' . $buttonUrl . '\'', 'class' => 'reset'],
                    -1
                );
            }
        }
    }

    /**
     * Check is allowed access
     *
     * @return bool
     */
    protected function isAllowed() : bool
    {
        return $this->authorization->isAllowed('Magento_Sales::actions_edit');
    }
}
