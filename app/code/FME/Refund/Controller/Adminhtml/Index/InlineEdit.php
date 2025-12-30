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

    use FME\Refund\Model\RefundFactory;

    class InlineEdit extends \Magento\Backend\App\Action
    {

        protected $jsonFactory;
        protected $modelFactory;

        public function __construct(
            \Magento\Backend\App\Action\Context $context,
            \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,RefundFactory $modelFactory
        ) {
            parent::__construct($context);
            $this->jsonFactory = $jsonFactory;
            $this->modelFactory = $modelFactory;
        }

        public function execute()
        {
            /* @var \Magento\Framework\Controller\Result\Json $resultJson */
            $resultJson = $this->jsonFactory->create();
            $error = false;
            $messages = [];

            if ($this->getRequest()->getParam('isAjax')) {
                $sroreitem = $this->getRequest()->getParam('items', []);
                if (!count($sroreitem)) {
                    $messages[] = __('Please correct the data sent.');
                    $error = true;
                } else {
                    foreach (array_keys($sroreitem) as $entityId) {
                        /* load your model to update the data */
                        $model = $this->modelFactory->create()->load($entityId);
                        try {
                            $model->setData(array_merge($model->getData(), $sroreitem[$entityId]));
                            $model->save();
                        } catch (\Exception $e) {
                            $messages[] = "[Error:]  {$e->getMessage()}";
                            $error = true;
                        }
                    }
                }
            }

            return $resultJson->setData([
                'messages' => $messages,
                'error' => $error
            ]);
        }
    }
