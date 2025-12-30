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
    use \Magento\Framework\Controller\ResultFactory;
    use \FME\Refund\Helper\Email;
    use \FME\Refund\Model\Order\Processor\Processor;

    class Accept extends \Magento\Backend\App\Action
    {
        protected $modelFactory;
        protected $_orderFactory;


        public function __construct(Context $context, 
        RefundFactory $modelFactory,
        \FME\Refund\Helper\Email $helperData,
         Processor $processor,
         \FME\Refund\Helper\Data $helper,
         \Magento\Sales\Model\OrderFactory $orderFactory
         )
        {
            $this->modelFactory = $modelFactory;
            $this->processor = $processor;
            $this->helperData = $helperData;
            $this->_orderFactory = $orderFactory;

            $this->helper = $helper; 
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

                        //getting order repository
                        $order = $this->_orderFactory->create()->load($order_id);
                        
                        $CheckOrderInvoice = $this->processor->refundCode($order_id);

                        if($CheckOrderInvoice == 1)
                        {

                            $model->setData('status','approved');
                            $order->setData('refund_status','approved');
                            $model->save();
                            $order->save();
                            
                            $this->processor->refundCode($order_id);

                            if( $this->helper->getGeneralConfig("enableemail"))
                            {
                                //Send the email to the customer
                                $this->helperData->SendAcceptEmailToCustomer($customer_name,$customer_email,$order_id);

                            }
                            $this->messageManager->addSuccess(__('The Request has been Approved...'));

                        }
                        else
                        {

                            return $resultRedirect->setPath('sales/order_invoice/new', ['order_id' => $order_id]);

                        }

      
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
