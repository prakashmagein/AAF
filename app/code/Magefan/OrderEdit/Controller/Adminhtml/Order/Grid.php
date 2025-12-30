<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Controller\Adminhtml\Order;

use Magento\Backend\App\Action\Context;

class Grid extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Sales::actions_edit';

    /**
     * Constructor
     *
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * Show product grid for custom options import popup
     *
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $block = $this->_view->getLayout()->createBlock(
            \Magefan\OrderEdit\Block\Adminhtml\Renderer\GridElement\Grid::class,
            'customer.grid'
        );

        return $this->getResponse()->setBody($block->toHtml());
    }
}
