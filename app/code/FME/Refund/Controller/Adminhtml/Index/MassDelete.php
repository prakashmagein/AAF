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

    class MassDelete extends \Magento\Backend\App\Action
    {

        protected $filter;
        protected $collectionFactory;

        public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory)
        {
            $this->filter = $filter;
            $this->collectionFactory = $collectionFactory;
            parent::__construct($context);
        }

        /**
         * @throws \Magento\Framework\Exception\LocalizedException
         */
        public function execute()
        {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $collectionSize = $collection->getSize();

            foreach ($collection as $module) {
                $module->delete();
            }

            $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $collectionSize));

            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath('*/*/');
        }
    }
