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
namespace Magepow\OnestepCheckout\Block\Adminhtml\Order\View;

use Magento\Framework\Registry;
use Magento\Sales\Model\Order;

class Comment extends \Magento\Backend\Block\Template
{
   protected $order;

   public function __construct(
      \Magento\Sales\Model\Order $order,
       \Magento\Backend\Block\Template\Context $context,
       array $data = []
   )
   {
      
      $this->order = $order;
       parent::__construct($context, $data);
   }

   public function getOrderComment()
    {      
      $order_id= $this->getRequest()->getParam('order_id');  
      $order =  $this->order->load($order_id)->getOnestepcheckoutOrderComment();
      return $order;

    }

    public function getOrderDateTime()
    {      
      $order_id= $this->getRequest()->getParam('order_id');  
      $order =  $this->order->load($order_id)->getOnestepcheckoutDeliveryTime();
      return $order;

    }
}