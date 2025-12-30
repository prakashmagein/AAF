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

namespace FME\Refund\Controller\Index;

use FME\Refund\Helper\Data;
use FME\Refund\Helper\Email;
use FME\Refund\Model\RefundFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;

class RequestSave extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
    protected $_messageManager;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        DateTime $date,
        Email $emailData,
        Data $helperData,
        RefundFactory $modelFactory
    ) {
        $this->_pageFactory = $pageFactory;
        $this->date = $date;
        $this->modelFactory = $modelFactory;
        $this->helperData = $helperData;
        $this->emailData = $emailData;
        $this->_messageManager = $messageManager;
        return parent::__construct($context);
    }

    public function execute()
    {


        if ($this->getRequest()->getParam('id')) {

            try {

                //getting post data
                $data['refund_id'] = null;
                $data['order_id'] = $this->getRequest()->getParam('id');
                $data['customer_name'] = $this->getRequest()->getParam('name');
                $data['customer_email'] = $this->getRequest()->getParam('email');
                $data['refund_reason'] = null;
                $data['description'] = null;
                $data['date'] = $this->date->gmtDate();

                $request = $this->modelFactory->create()->getCollection()
                ->addFieldToFilter('order_id', $data['order_id']);

                if(!$request->getData())
                {
                    $model = $this->modelFactory->create();
                    $model->setData($data);
                    $model->save();
    
                    if ($this->helperData->getGeneralConfig('enableemail')) {
                        $this->emailData->SendNotificationEmailToCustomer($data['customer_name'], $data['customer_email'], $data['order_id']);
                        $this->emailData->SendNotificationEmailToAdmin($data['customer_name'], $data['customer_email'], $data['order_id'], $data['refund_reason'], $data['description']);
                    }
    
                    $this->_messageManager->addSuccess('Your Request has been recieved !!');
                }
                else {
                $this->_messageManager->addSuccess('Request is already submitted !!');

                }

                

            } catch (\Exception $e) {
                $this->_messageManager->addError('Sorry there us some error sending emial or accepting your request \n' . $e->getMessage());

            }
        } else {
            $this->_messageManager->addError('Sorry there is some problem accepting your request we will try to resolve this issue ASAP !! \n');

        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('sales/order/history');

    }
}
