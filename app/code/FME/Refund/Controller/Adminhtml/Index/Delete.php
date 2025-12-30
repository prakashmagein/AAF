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

    class Delete extends \Magento\Backend\App\Action
    {
        protected $modelFactory;
        public function __construct(Context $context, RefundFactory $modelFactory)
        {
            $this->modelFactory = $modelFactory;
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
                    $model->delete();
                    $this->messageManager->addSuccess(__('The Module has been deleted.'));

                    return $resultRedirect->setPath('*/*/');
                }
                catch (\Exception $e)
                {
                    // display error message
                    $this->messageManager->addError($e->getMessage());

                }
            }
            // display error message
            $this->messageManager->addError(__('We can\'t find a Module to delete.'));
            // go to grid
            return $resultRedirect->setPath('*/*/');
        }
    }
