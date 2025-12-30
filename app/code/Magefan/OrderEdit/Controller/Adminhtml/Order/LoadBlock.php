<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Controller\Adminhtml\Order;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Sales\Controller\Adminhtml\Order\Create as CreateAction;
use Magento\Catalog\Helper\Product;
use Magento\Framework\Escaper;
use Magefan\OrderEdit\Block\Adminhtml\Order\Edit\Form;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Registry;
use Magefan\OrderEdit\Model\Quote\TaxManager;

class LoadBlock extends CreateAction implements HttpPostActionInterface, HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Sales::actions_edit';

    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var TaxManager
     */
    protected $taxManager;

    /**
     * @param Action\Context $context
     * @param Product $productHelper
     * @param Escaper $escaper
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     * @param RawFactory $resultRawFactory
     * @param ResourceConnection $resourceConnection
     * @param Registry $registry
     * @param TaxManager $taxManager
     */
    public function __construct(
        Action\Context $context,
        Product $productHelper,
        Escaper $escaper,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        RawFactory $resultRawFactory,
        ResourceConnection $resourceConnection,
        Registry $registry,
        TaxManager $taxManager
    ) {
        $this->resultRawFactory = $resultRawFactory;
        $this->resourceConnection = $resourceConnection;
        $this->registry = $registry;
        $this->taxManager = $taxManager;
        parent::__construct(
            $context,
            $productHelper,
            $escaper,
            $resultPageFactory,
            $resultForwardFactory
        );
    }

    /**
     * Loading page block
     *
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $request = $this->getRequest();
        $formType = (int)$this->_request->getParam('form_type');

        //When updating shipping it will be also recalculate taxes depend on address,we need recalculate depend on mf_tax_rate_id
        if ($request->getParam('update_items')) {
            $this->taxManager->addTaxRate((int)$request->getParam('tax_rate_id'));
        } else {
            $this->taxManager->addTaxRate((int)$this->_getQuote()->getData('mf_tax_rate_id'));
        }

        try {
            $this->_initSession()->_processData();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_reloadQuote();
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->_reloadQuote();
            $this->messageManager->addExceptionMessage($e, $e->getMessage());
        }

        $asJson = $request->getParam('json');
        $block = $request->getParam('block');

        /**
         * @var \Magento\Framework\View\Result\Page $resultPage
         */
        $resultPage = $this->resultPageFactory->create();

        $resultPage->addHandle($asJson ? 'sales_order_create_load_block_json' : 'sales_order_create_load_block_plain');

        if ($block) {
            $blocks = explode(',', $block);

            if ($asJson && !in_array('message', $blocks)) {
                $blocks[] = 'message';
            }

            $lock = ['sidebar', 'billing_method', 'shipping_method',  'giftmessage', 'shipping_address'];

            if (in_array($formType, [Form::PAYMENT_METHOD_EDIT_FORM, Form::ALL_TYPES_EDIT_FORM])) {
                unset($lock[1]);
            }

            if (in_array($formType, [Form::SHIPPING_METHOD_EDIT_FORM, Form::ALL_TYPES_EDIT_FORM])) {
                unset($lock[2]);
            }

            foreach ($blocks as $block) {
                if (in_array($block, $lock)) {
                    continue;
                }

                $resultPage->addHandle('sales_order_create_load_block_' . $block);
            }
        }

        $result = $resultPage->getLayout()->renderElement('content');

        if ($request->getParam('as_js_varname')) {
            $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setUpdateResult($result);

            $resultRaw = $this->resultRawFactory->create();
            $session = $this->_objectManager->get(\Magento\Backend\Model\Session::class);
            if ($session->hasUpdateResult() && is_scalar($session->getUpdateResult())) {
                $resultRaw->setContents($session->getUpdateResult());
            }
            $session->unsUpdateResult();
            return $resultRaw;
        }

        return $this->resultRawFactory->create()->setContents($result);
    }

    public function resetShipping()
    {
        try {
            $this->getRequest()->setPostValue('reset_shipping', 1);
            $this->_initSession()->_processData();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_reloadQuote();
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->_reloadQuote();
            $this->messageManager->addExceptionMessage($e, $e->getMessage());
        }
    }
}
