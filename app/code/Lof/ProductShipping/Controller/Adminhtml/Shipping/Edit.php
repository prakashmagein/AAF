<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_ProductShipping
 * @copyright  Copyright (c) 2016 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\ProductShipping\Controller\Adminhtml\Shipping;

class Edit extends \Lof\ProductShipping\Controller\Adminhtml\Shipping
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context        $context
     * @param \Magento\Framework\Registry                $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_ProductShipping::edit_shipping');
    }

    /**
     * Edit ProductShipping Form
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id    = $this->getRequest()->getParam('lofshipping_id');
        $model = $this->_objectManager->create('Lof\ProductShipping\Model\Shipping');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if ( ! $model->getId()) {
                $this->messageManager->addError(__('This Shipping no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        // 3. Set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if ( ! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in forms
        $this->_coreRegistry->register('lofproductshipping_shipping', $model);
        $resultPage = $this->resultPageFactory->create();

        // 5. Build edit form
        //$this->getRequest()->getRequestUri();
        if (strpos($this->getRequest()->getRequestUri(), 'new')) {
            $text = __('New Shipping');
        } else {
            $text = __('Edit Shipping');
        }

        $this->initPage($resultPage)->addBreadcrumb($text, $text);
        $resultPage->getConfig()->getTitle()->prepend($text);
        $resultPage->getConfig()->getTitle()->prepend($text);

        return $resultPage;
    }
}
