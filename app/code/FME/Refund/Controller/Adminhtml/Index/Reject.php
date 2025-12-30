<?php
/**
* FME Extensions
*
* NOTICE OF LICENSE
*
* This source file is subject to the fmeextensions.com license that is
* available through the world-wide-web at this URL:
* https://www.fmeextensions.com/LICENSE.txt
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this extension to newer
* version in the future.
*
* @category  FME
* @author    Hassan <support@fmeextensions.com>
* @package   FME_Refund
* @copyright Copyright (c) 2021 FME (http://fmeextensions.com/)
* @license   https://fmeextensions.com/LICENSE.txt
*/

namespace FME\Refund\Controller\Adminhtml\Index;

use \FME\Refund\Model\RefundFactory;
use \Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use FME\Refund\Helper\Email;
use FME\Refund\Helper\Data;

class Reject extends \Magento\Backend\App\Action
{
    protected $modelFactory;
    protected $orderManagement;
    protected $_orderFactory;


    public function __construct(Context $context, RefundFactory $modelFactory,Email $helperData
    ,\Magento\Sales\Api\OrderManagementInterface $orderManagement,Data $helper,
    \Magento\Sales\Model\OrderFactory $orderFactory

    )
    {
        $this->modelFactory = $modelFactory;
        $this->_orderFactory = $orderFactory;
        $this->helperData = $helperData;
        $this->helper = $helper; 
        $this->orderManagement = $orderManagement;
        parent::__construct($context);
    }

    public function execute()
    {   
        // getting parameter id from action class
        $id = $this->getRequest()->getParam('refund_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try
            {
                $model = $this->modelFactory->create()->load($id);

                if($model->getData())
                {
                    $customer_name = $model->getData("customer_name");
                    $customer_email = $model->getData("customer_email");
                    $order_id = $model->getData("order_id");
                    $order = $this->_orderFactory->create()->load($order_id);

                        $model->setData('status','rejected');
                        $order->setData('refund_status','rejected');
                        $model->save();
                        $order->save();
                        
                        if( $this->helper->getGeneralConfig("enableemail"))
                        {
                        //Send the email to the customer
                        $this->helperData->SendRejectEmailToCustomer($customer_name,$customer_email,$order_id);
                        }

                        $this->messageManager->addSuccess(__('The Request has been Rejected...'));
                }
                return $resultRedirect->setPath('*/*/');
            }
            catch (\Exception $e)
            {
                // display error message
                $this->messageManager->addError($e->getMessage());
            }
        }
        // display error message
        $this->messageManager->addError(__('Ooops There is some problem accepting this request'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
}
}
