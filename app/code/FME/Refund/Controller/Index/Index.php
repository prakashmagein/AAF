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
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Index extends \Magento\Framework\App\Action\Action
{
    private $modelFactory;
    protected $date;
    protected $_messageManager;

    public function __construct(Context $context, RefundFactory $modelFactory, Data $helperData, DateTime $date, Email $emailData, \Magento\Framework\Message\ManagerInterface $messageManager)
    {
        $this->modelFactory = $modelFactory;
        $this->helperData = $helperData;
        $this->emailData = $emailData;
        $this->date = $date;
        $this->_messageManager = $messageManager;
        parent::__construct($context);
    }

    public function ValidateRecaptcha()
    {
        $post = (array) $this->getRequest()->getPost();
        $check = 'true';
        if ($this->helperData->getRecaptchaConfig('recaptcha')) {
            $request = $this->getRequest();
            $remoteAddress = new \Magento\Framework\Http\PhpEnvironment\RemoteAddress($this->getRequest());
            $visitorIp = $remoteAddress->getRemoteAddress();

            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $secret = $this->helperData->getRecaptchaConfig('secretkey');

            $response = null;
            $path = 'https://www.google.com/recaptcha/api/siteverify?';
            $secretKey = $secret;
            $response = $post['g-recaptcha-response'];
            $remoteIp = $visitorIp;

            $response = file_get_contents($path."secret=$secretKey&response=$response&remoteip=$remoteIp");
            $answers = json_decode($response, true);
            if (trim($answers['success']) != true) {
                $check = 'false';
            }
        }

        return $check;
    }

    public function execute()
    {
        if ($this->ValidateRecaptcha()) {
            $post = (array) $this->getRequest()->getPost();
            if (!empty($post)) {
                try {
                    // Retrieve your form data
                    $data['refund_id'] = null;
                    $data['customer_name'] = $post['customer_name'];
                    $data['customer_email'] = $post['customer_email'];
                    $data['refund_reason'] = $post['refund_reason'];
                    if ($this->helperData->getPopupConfig('description')) {
                        $data['description'] = $post['description'];
                    } else {
                        $data['description'] = null;
                    }
                    $data['date'] = $this->date->gmtDate();
                    $data['order_id'] = $post['order_id'];

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

                    $this->_messageManager->addSuccess('Your Request has been Submitted !!');
                    }
                    else {
                        
                        $this->_messageManager->addSuccess(' Request Already Submitted Please wait for response !!');

                    }
                } catch (\Exception $e) {
                    // display error message
                    $this->_messageManager->addError($e->getMessage());
                }

                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $this->_messageManager->addSuccess('Your Request has been Submitted !!');
                return $resultRedirect->setPath('sales/order/history');
            } else {
                $this->_messageManager->addError('Ooops There is some problem submitting this request !!');
            }
            $this->_messageManager->addError(__('Ooops There is some problem accepting this request'));
        }
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
