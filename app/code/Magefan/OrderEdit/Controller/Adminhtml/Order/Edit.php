<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Controller\Adminhtml\Order;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magefan\OrderEdit\Model\Config;
use Magento\Sales\Model\OrderRepository;
use Magefan\OrderEdit\Block\Adminhtml\Order\Edit\Form;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Backend\Model\Session\Quote as SessionQuote;
use Magento\Sales\Model\AdminOrder\Create;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magefan\OrderEdit\Model\IsEditAllowed;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Sales::actions_edit';

    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var SessionQuote
     */
    private $sessionQuote;

    /**
     * @var Create
     */
    private $createOrder;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var IsEditAllowed
     */
    private $isEditAllowed;

    /**
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param Config $config
     * @param OrderRepository $orderRepository
     * @param CartRepositoryInterface|null $quoteRepository
     * @param SessionQuote|null $sessionQuote
     * @param Create|null $createOrder
     * @param CustomerRepositoryInterface|null $customerRepository
     * @param IsEditAllowed|null $isEditAllowed
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        Config $config,
        OrderRepository $orderRepository,
        CartRepositoryInterface $quoteRepository = null,
        SessionQuote $sessionQuote = null,
        Create $createOrder = null,
        CustomerRepositoryInterface $customerRepository = null,
        IsEditAllowed $isEditAllowed = null
    ) {
        $this->pageFactory = $pageFactory;
        $this->config = $config;
        $this->orderRepository = $orderRepository;
        $this->quoteRepository = $quoteRepository ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(CartRepositoryInterface::class);
        $this->sessionQuote = $sessionQuote ?:  \Magento\Framework\App\ObjectManager::getInstance()
            ->get(SessionQuote::class);
        $this->createOrder = $createOrder ?:  \Magento\Framework\App\ObjectManager::getInstance()
            ->get(Create::class);
        $this->customerRepository = $customerRepository ?:  \Magento\Framework\App\ObjectManager::getInstance()
            ->get(CustomerRepositoryInterface::class);
        $this->isEditAllowed = $isEditAllowed ?:  \Magento\Framework\App\ObjectManager::getInstance()
            ->get(IsEditAllowed::class);
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $orderId = (int)$this->_request->getParam('order_id');

        if (!$orderId) {
            $resultRedirect->setPath('sales/order/index');
            $this->messageManager->addErrorMessage(__('This order no longer exists.'));
            return $resultRedirect;
        }

        if (!$this->config->isEnabled()) {
            $this->messageManager->addError(
                __(
                    strrev(
                        'rotidE redrO > snoisnetxE nafegaM > noitarugifnoC > serotS ot etagivan esaelp noisnetxe eht elbane ot ,delbasid si tidE redrO nafegaM'
                    )
                )
            );
            return $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
        }

        try {
            $order = $this->orderRepository->get($orderId);

            [$isAllowed, $message] =  $this->isEditAllowed->execute($order);

            if (!$isAllowed) {
                $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
                $this->messageManager->addErrorMessage($message);
                return $resultRedirect;
            }

            try {
                $quote = $this->quoteRepository->get((int)$order->getQuoteId());
                if ((int)($quote->getCustomerId()) !== (int)($order->getCustomerId())) {
                    try {
                        $customer = $this->customerRepository->getById($order->getCustomerId());
                    } catch (NoSuchEntityException $e) {
                        $this->messageManager->addErrorMessage(__('This invalid customer id.'));
                        $resultRedirect->setPath('sales/order/index');
                        return $resultRedirect;
                    }

                    $quote->assignCustomer($customer);
                    $this->quoteRepository->save($quote);
                }
            } catch (NoSuchEntityException $e) {
                $formType = (int)$this->_request->getParam('form_type');
                $formTypeWithQuote = in_array($formType, [Form::PAYMENT_METHOD_EDIT_FORM, Form::SHIPPING_METHOD_EDIT_FORM,
                    Form::ITEMS_ORDERED_EDIT_FORM, Form::ALL_TYPES_EDIT_FORM]);
                if ($formTypeWithQuote) {
                    $this->sessionQuote->setCurrencyId($order->getOrderCurrencyCode());
                    $this->sessionQuote->setCustomerId($order->getCustomerId() ?: false);
                    $this->sessionQuote->setStoreId($order->getStoreId());

                    try {
                        $this->createOrder->initFromOrder($order);
                    } catch (\Exception $e) {
                        $this->messageManager->addErrorMessage(__($e->getMessage()));
                        $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
                        return $resultRedirect;
                    }

                    $order->setQuoteId($this->sessionQuote->getQuote()->getId());

                    $this->orderRepository->save($order);
                }

            }
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('This order no longer exists.'));
            $resultRedirect->setPath('sales/order/index');
            return $resultRedirect;
        }

        return $this->pageFactory->create();
    }
}
