<?php
/**
 * Comment
 *
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */
namespace Magepow\OnestepCheckout\Block\LayoutOnestep\Order\View;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;


class Comment extends Template
{
    /**
     * @type Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    )
    {
        $this->_coreRegistry = $registry;

        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getOrderComment()
    {
        if ($order = $this->getOrder()) {
            return $order->getOnestepcheckoutOrderComment();
        }
        return '';
    }

     /**
     * @return string
     */
    public function getOrderDateTime()
    {
        if ($order = $this->getOrder()) {
            return $order->getOnestepcheckoutDeliveryTime();
        }
        return '';
    }
    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }
}
