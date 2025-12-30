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
    use Magento\Framework\Controller\ResultFactory;
    use Magento\Backend\App\Action\Context;
    use Magento\Ui\Component\MassAction\Filter;
    use FME\Refund\Model\ResourceModel\Refund\CollectionFactory;
    use \FME\Refund\Model\RefundFactory;
    use FME\Refund\Helper\Email;
    use FME\Refund\Helper\Data;

    class MassReject extends \Magento\Backend\App\Action
    {

        protected $filter;
        protected $collectionFactory;
        protected $modelFactory;
        protected $_orderFactory;

        public function __construct(
        Context $context, 
        Filter $filter,
        RefundFactory $modelFactory, 
        CollectionFactory $collectionFactory,
        Data $helperData,
        Email $emailData,
        \Magento\Sales\Model\OrderFactory $orderFactory)
        {
            $this->filter = $filter;
            $this->collectionFactory = $collectionFactory;
            $this->modelFactory = $modelFactory;
            $this->_orderFactory = $orderFactory;
            $this->helperData = $helperData;
            $this->emailData = $emailData;
            parent::__construct($context);
        }

        /**
         * @throws \Magento\Framework\Exception\LocalizedException
         */
        public function execute()
        {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $collectionSize = $collection->getSize();

            foreach ($collection as $request) {

               $customer_name =  $request['customer_name'];
               $customer_email =  $request['customer_email'];
               $order_id = $request['order_id'];
               $order = $this->_orderFactory->create()->load($order_id);

                $request->setData('status','rejected');
                $order->setData('refund_status','rejected');

                $request->save();
                $order->save();


                if( $this->helperData->getGeneralConfig("enableemail") == 1)
                {
                    //Send the email to the customer
                    $this->emailData->SendRejectEmailToCustomer($customer_name,$customer_email,$order_id);
                }

            }

            $this->messageManager->addSuccess(__('A total of %1 Request(s) have been rejected.', $collectionSize));

            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath('*/*/');
        }
    }
