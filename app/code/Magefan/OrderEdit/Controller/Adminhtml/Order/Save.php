<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Sales\Model\OrderRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Backend\Model\Session\Quote as SessionQuote;
use Magento\Sales\Model\ResourceModel\GridPool;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magefan\OrderEdit\Model\Config;
use Magento\Backend\Model\Auth\Session as AuthSession;
use Magento\Sales\Model\Order;
use Magefan\OrderEdit\Model\Order\UpdateOrderItems;
use Magefan\OrderEdit\Model\Order\UpdateOrderAddress;
use Magefan\OrderEdit\Model\Order\UpdateOrderShippingMethod;
use Magefan\OrderEdit\Model\Order\UpdateOrderPaymentMethod;
use Magefan\OrderEdit\Model\Order\UpdateOrderIncrementId;
use Magefan\OrderEdit\Model\Order\UpdateOrderCreatedAt;
use Magefan\OrderEdit\Model\Order\UpdateOrderStatus;
use Magefan\OrderEdit\Model\Order\UpdateOrderPurchasedFrom;
use Magefan\OrderEdit\Block\Adminhtml\Order\Edit\Form;
use Magefan\OrderEdit\Model\HistoryFactory;
use Magefan\OrderEdit\Model\HistoryRepository;
use Magento\Framework\Exception\CouldNotSaveException;
use Magefan\OrderEdit\Model\Order\AmastyCashOnDeliveryHandler;
use Magefan\OrderEdit\Model\IsEditAllowed;

class Save extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Sales::actions_edit';

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var SessionQuote
     */
    private $sessionQuote;

    /**
     * @var GridPool
     */
    private $entityGrid;

    /**
     * @var string
     */
    protected $_formSessionKey  = 'order_edit_form_data';

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var AuthSession
     */
    private $authSession;

    /**
     * @var Order
     */
    private $order;

    /**
     * @var Quote
     */
    private $quote;

    private $updateOrderItems;

    /**
     * @var UpdateOrderAddress
     */
    private $updateOrderAddress;

    /**
     * @var UpdateOrderShippingMethod
     */
    private $updateOrderShippingMethod;

    /**
     * @var UpdateOrderPaymentMethod
     */
    private $updateOrderPaymentMethod;

    /**
     * @var UpdateOrderIncrementId
     */
    private $updateOrderIncrementId;

    /**
     * @var UpdateOrderCreatedAt
     */
    private $updateOrderCreatedAt;

    /**
     * @var UpdateOrderStatus
     */
    private $updateOrderStatus;

    /**
     * @var UpdateOrderPurchasedFrom
     */
    private $updateOrderPurchasedFrom;

    /**
     *
     * @var HistoryFactory
     */
    private $historyFactory;

    /**
     * @var HistoryRepository
     */
    private $historyRepository;

    /**
     * @var IsEditAllowed
     */
    private $isEditAllowed;

    private $amastyExtraFeeCollectionFactory;

    /**
     * @var AmastyCashOnDeliveryHandler
     */
    private $amastyCashOnDeliveryHandler;

    /**
     * @param Action\Context $context
     * @param OrderRepository $orderRepository
     * @param CartRepositoryInterface $quoteRepository
     * @param CollectionFactory $collectionFactory
     * @param Config $config
     * @param SessionQuote $sessionQuote
     * @param GridPool $entityGrid
     * @param DataPersistorInterface $dataPersistor
     * @param AuthSession $authSession
     * @param UpdateOrderItems $updateOrderItems
     * @param UpdateOrderAddress $updateOrderAddress
     * @param UpdateOrderShippingMethod $updateOrderShippingMethod
     * @param UpdateOrderPaymentMethod $updateOrderPaymentMethod
     * @param UpdateOrderIncrementId $updateOrderIncrementId
     * @param UpdateOrderCreatedAt $updateOrderCreatedAt
     * @param UpdateOrderStatus $updateOrderStatus
     * @param UpdateOrderPurchasedFrom $updateOrderPurchasedFrom
     * @param HistoryFactory $historyFactory
     * @param HistoryRepository $historyRepository
     * @param IsEditAllowed $isEditAllowed
     * @param AmastyCashOnDeliveryHandler $amastyCashOnDeliveryHandler
     */
    public function __construct(
        Action\Context $context,
        OrderRepository $orderRepository,
        CartRepositoryInterface $quoteRepository,
        CollectionFactory $collectionFactory,
        Config  $config,
        SessionQuote          $sessionQuote,
        GridPool  $entityGrid,
        DataPersistorInterface $dataPersistor,
        AuthSession            $authSession,
        UpdateOrderItems $updateOrderItems,
        UpdateOrderAddress $updateOrderAddress,
        UpdateOrderShippingMethod $updateOrderShippingMethod,
        UpdateOrderPaymentMethod $updateOrderPaymentMethod,
        UpdateOrderIncrementId $updateOrderIncrementId,
        UpdateOrderCreatedAt $updateOrderCreatedAt,
        UpdateOrderStatus $updateOrderStatus,
        UpdateOrderPurchasedFrom $updateOrderPurchasedFrom,
        HistoryFactory $historyFactory,
        HistoryRepository $historyRepository,
        IsEditAllowed $isEditAllowed,
        AmastyCashOnDeliveryHandler $amastyCashOnDeliveryHandler
    ) {
        parent::__construct($context);
        $this->orderRepository = $orderRepository;
        $this->quoteRepository = $quoteRepository;
        $this->collectionFactory = $collectionFactory;
        $this->config = $config;
        $this->sessionQuote = $sessionQuote;
        $this->entityGrid = $entityGrid;
        $this->dataPersistor = $dataPersistor;
        $this->authSession = $authSession;
        $this->updateOrderItems = $updateOrderItems;
        $this->updateOrderAddress = $updateOrderAddress;
        $this->updateOrderShippingMethod = $updateOrderShippingMethod;
        $this->updateOrderPaymentMethod = $updateOrderPaymentMethod;
        $this->updateOrderIncrementId = $updateOrderIncrementId;
        $this->updateOrderCreatedAt = $updateOrderCreatedAt;
        $this->updateOrderStatus = $updateOrderStatus;
        $this->updateOrderPurchasedFrom = $updateOrderPurchasedFrom;
        $this->historyFactory = $historyFactory;
        $this->historyRepository = $historyRepository;
        $this->isEditAllowed = $isEditAllowed;
        $this->amastyCashOnDeliveryHandler = $amastyCashOnDeliveryHandler;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\InputException
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $orderId = (int)$this->_request->getParam('order_id');

        if (!$orderId) {
            $resultRedirect->setPath('sales/order/index');
            $this->messageManager->addErrorMessage(__('The order ID does not provided.'));

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

        $postData = $this->_request->getPostValue();

        unset($postData['form_key']);

        try {
            $this->order = $this->orderRepository->get($orderId);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('This order no longer exists.'));
            $resultRedirect->setPath('sales/order/index');

            return $resultRedirect;
        }

        try {
            $this->quote = $this->quoteRepository->get((int)$this->order->getQuoteId());
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('This quote no longer exists.'));
            $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
            return $resultRedirect;
        }

        $changeSections = [];

        [$isAllowed, $message] = $this->isEditAllowed->execute($this->order);

        if (!$isAllowed) {
            $this->messageManager->addErrorMessage($message);
            $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
            return $resultRedirect;
        }

        $collection = $this->collectionFactory->create();

        //Update Order Items
        //Update Order Items
        if (in_array($this->_request->getParam('form_type'), [Form::ITEMS_ORDERED_EDIT_FORM, Form::ALL_TYPES_EDIT_FORM])) {
            $statusOfUpdatingOrderItems = $this->updateOrderItems->execute(
                $this->order,
                $changeSections,
                $this->sessionQuote->getQuote()
            );

            if (!$statusOfUpdatingOrderItems) {
                $this->messageManager->addErrorMessage(__('This item no longer exists.'));
                $resultRedirect->setPath(
                    'mforderedit/order/edit',
                    ['order_id' => $orderId, 'form_type' => $this->_request->getParam('form_type')]
                );

                return $resultRedirect;
            }

            $editShippingMethoudUrl = $this->getUrl(
                'mforderedit/order/edit',
                ['order_id' => $this->order->getId(), 'form_type' => Form::SHIPPING_METHOD_EDIT_FORM]
            );

            $this->messageManager->addNoticeMessage(
                __('Shipping Total Amount may be changed. Please <a href="%1">review shipping methods section</a>.', $editShippingMethoudUrl)
            )->getMessages()->getLastAddedMessage()->setIdentifier('MfOrder');
        }

        foreach ($postData as $key => $value) {
            switch ($key) {
                case 'purchased_from': {
                    $newPurchasedFrom = (int)$value;
                    $statusOfUpdatingPurchasedFrom = $this->updateOrderPurchasedFrom->execute(
                        $this->order,
                        $changeSections,
                        null,
                        $newPurchasedFrom
                    );
                    if (!$statusOfUpdatingPurchasedFrom) {
                        $this->_setFormData($postData);
                        $this->messageManager->addErrorMessage(__("Invalid store ID '$newPurchasedFrom'"));
                        $resultRedirect->setPath(
                            'mforderedit/order/edit',
                            ['order_id' => $orderId,'form_type' => $this->_request->getParam('form_type')]
                        );
                        return $resultRedirect;
                    }

                } break;
                case 'status': {
                    $newStatus = $value;
                    $this->updateOrderStatus->execute($this->order, $changeSections, null, $newStatus, $collection);

                } break;
                case 'order_date': {
                    $newOrderDate = $value;

                    $statusOfUpdatingOrderCreatedAt = $this->updateOrderCreatedAt->execute(
                        $this->order,
                        $changeSections,
                        null,
                        $newOrderDate
                    );

                    if (!$statusOfUpdatingOrderCreatedAt) {
                        $this->_setFormData($postData);
                        $this->messageManager->addErrorMessage(__("Invalid input datetime format of value '$newOrderDate'"));
                        $resultRedirect->setPath(
                            'mforderedit/order/edit',
                            ['order_id' => $orderId,'form_type' => $this->_request->getParam('form_type')]
                        );
                        return $resultRedirect;
                    }

                } break;
                case 'increment_id': {
                    $newOrderIncrementId = $value;
                    $statusOfUpdatingIncrementId = $this->updateOrderIncrementId->execute(
                        $this->order,
                        $changeSections,
                        null,
                        $newOrderIncrementId,
                        $collection
                    );

                    if (!$statusOfUpdatingIncrementId) {
                        $this->_setFormData($postData);
                        $this->messageManager->addErrorMessage(__('The order ID already exists.'));
                        $resultRedirect->setPath(
                            'mforderedit/order/edit',
                            ['order_id' => $orderId,'form_type' => $this->_request->getParam('form_type')]
                        );
                        return $resultRedirect;
                    }
                } break;

                default : {
                    $isValueArray = is_array($value);

                    $quickEdit = (Form::ALL_TYPES_EDIT_FORM === (int)$this->_request->getParam('form_type'));

                    $isShippingMethodInRequest = $isValueArray && isset($value['shipping_method']);
                    $isPaymentMethodInRequest = $isValueArray && isset($value['method']);
                    $isBillingAddressInRequest = $isValueArray && isset($value['billing_address']) && $quickEdit;
                    $isShippingAddressInRequest = $isValueArray && isset($value['shipping_address']) && $quickEdit;

                    $saveQuote = false;

                    if ($isShippingMethodInRequest) {
                        $newShippingMethod = $value['shipping_method'];

                        $statusOfUpdatingShippingMethod = $this->updateOrderShippingMethod->execute(
                            $this->order,
                            $changeSections,
                            $this->quote,
                            $newShippingMethod,
                            (string)$this->_request->getParam('mf-custom-shipping-price')
                        );

                        if (!$statusOfUpdatingShippingMethod) {
                            $this->_setFormData($postData);
                            $this->messageManager->addErrorMessage(__('Invalid shipping method'));
                            $resultRedirect->setPath(
                                'mforderedit/order/edit',
                                ['order_id' => $orderId, 'form_type' => $this->_request->getParam('form_type')]
                            );
                            return $resultRedirect;
                        }

                        $saveQuote = true;
                    }

                    if ($isPaymentMethodInRequest) {
                        $newPaymentMethod = (string)$value['method'];
                        $poNumber = (string)($value['po_number'] ?? '');
                        $statusOfUpdatingPaymentMethod = $this->updateOrderPaymentMethod->execute(
                            $this->order,
                            $changeSections,
                            $this->quote,
                            $newPaymentMethod,
                            $poNumber
                        );

                        if (!$statusOfUpdatingPaymentMethod) {
                            $this->_setFormData($postData);
                            $this->messageManager->addErrorMessage(__('This payment method is not available.'));
                            $resultRedirect->setPath(
                                'mforderedit/order/edit',
                                ['order_id' => $orderId,'form_type' => $this->_request->getParam('form_type')]
                            );
                            return $resultRedirect;
                        }
                        $saveQuote = true;
                    }

                    if ($isShippingAddressInRequest) {
                        $shippingAddress = $value['shipping_address'];
                        $this->updateOrderAddress->execute(
                            $this->order,
                            $changeSections,
                            $this->quote,
                            $shippingAddress,
                            'shipping'
                        );
                        $saveQuote = true;
                    }

                    if ($isBillingAddressInRequest) {
                        $billingAddress = $value['billing_address'];
                        $this->updateOrderAddress->execute(
                            $this->order,
                            $changeSections,
                            $this->quote,
                            $billingAddress,
                            'billing'
                        );
                        $saveQuote = true;
                    }

                    if ($saveQuote) {
                        try {
                            $this->quoteRepository->save($this->quote);
                        } catch (CouldNotSaveException $e) {
                            $this->messageManager->addErrorMessage(__('This quote no longer exists.'));
                            $resultRedirect->setPath(
                                'mforderedit/order/edit',
                                ['order_id' => $orderId,'form_type' => $this->_request->getParam('form_type')]
                            );
                            return $resultRedirect;
                        }
                    }

                    if ($value && (false !== strpos($key, 'dob'))) {
                        $newCustomerDob = $value;

                        if (!$this->updateOrderCreatedAt->isValidDate((string)$newCustomerDob)) {
                            $this->_setFormData($postData);
                            $this->messageManager->addErrorMessage(__("Invalid input datetime format of value '$value'"));
                            $resultRedirect->setPath(
                                'mforderedit/order/edit',
                                ['order_id' => $orderId,'form_type' => $this->_request->getParam('form_type')]
                            );
                            return $resultRedirect;
                        }
                    }

                    $allowed = ['col-prefix','col-firstname','col-lastname','col-middlename','col-suffix','col-email',
                        'col-group_id','col-dob','col-taxvat','col-gender'];
                    if (!isset($postData['col-gender']) && array_intersect(array_keys($postData), array_values($allowed))) {
                        $orderCurrentCustomerGender = (string)$this->order->getCustomerGender();

                        if ($orderCurrentCustomerGender!== '') {
                            $this->writeChanges(
                                'customer',
                                $changeSections,
                                'customer_gender',
                                'customer_gender',
                                $orderCurrentCustomerGender,
                                ''
                            );

                            $this->order->setCustomerGender('');
                            unset($allowed[count($allowed)-1]);
                        }
                    }

                    if (in_array($key, $allowed)) {
                        $customerKey = (string)str_replace('col-', 'customer_', $key);
                        $newCustomerField = $value;
                        $orderCurrentCustomerField = (string)$this->order->getData($customerKey);

                        if ($newCustomerField === '' && $customerKey === 'customer_gender') {
                            $newCustomerField = '0';
                        }

                        if ($orderCurrentCustomerField !== $newCustomerField) {
                            $this->writeChanges(
                                'customer',
                                $changeSections,
                                $customerKey,
                                $customerKey,
                                $orderCurrentCustomerField,
                                $newCustomerField
                            );
                            $this->order->setData($customerKey, $newCustomerField);
                        }

                    }
                }
            }
        }

        if (count($changeSections)) {


            $userName = $this->authSession->getUser()->getName();
            $comment = '';

            $sectionComments = [];
            foreach ($changeSections as $sectionName => $section) {
                $sectionComments[$sectionName] = '';
                foreach ($section as $changes) {
                    $sectionComments[$sectionName] .= $userName . " changed: " . PHP_EOL .
                        $changes['name_of_field'] . " from ". $changes['old_value'] . " to " . $changes['new_value'] . PHP_EOL;
                }
            }

            foreach ($sectionComments as $sectionName => $sectionComment) {
                $history = $this->historyFactory->create()->setOrderId(
                    $this->order->getId()
                )->setComment(
                    $sectionComment
                )->setCommentSection(
                    $sectionName
                )->setStatus(
                    $this->order->getStatus()
                );

                try {
                    $this->historyRepository->save($history);
                } catch (CouldNotSaveException $e) {

                }
            }

            //$this->order->addCommentToStatusHistory(nl2br($comment));

        }

        if ($this->getAmastyExtraFeeCollectionFactory()) {
            try {
                $feeQuote = $this->amastyExtraFeeCollectionFactory->create()->getFeeByQuoteId($this->order->getQuoteId());

                $this->order->setGrandTotal(
                    (float)$this->order->getGrandTotal()
                    + (float)($feeQuote['fee_amount'] ?? 0)
                    + (float)($feeQuote['tax_amount'] ?? 0)
                );

                $this->order->setBaseGrandTotal(
                    (float)$this->order->getBaseGrandTotal()
                    + (float)($feeQuote['base_fee_amount'] ?? 0)
                    + (float)($feeQuote['base_tax_amount'] ?? 0)
                );
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {

            }
        }

        $this->amastyCashOnDeliveryHandler->execute($this->order);

        try {
            $this->orderRepository->save($this->order);
        } catch (CouldNotSaveException $e) {
            $this->messageManager->addErrorMessage(__('This order no longer exists.'));
            $resultRedirect->setPath(
                'mforderedit/order/edit',
                ['order_id' => $orderId,'form_type' => $this->_request->getParam('form_type')]
            );
            return $resultRedirect;
        }

        $this->entityGrid->refreshByOrderId((string)$this->order->getId());
        $this->messageManager->addSuccessMessage(__('Succsess save.'));
        return $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
    }

    /**
     * @return false
     */
    protected function getAmastyExtraFeeCollectionFactory()
    {
        if (null === $this->amastyExtraFeeCollectionFactory) {
            $this->amastyExtraFeeCollectionFactory = false;
            if (class_exists('Amasty\Extrafee\Model\ResourceModel\ExtrafeeQuote\CollectionFactory')) {
                $this->amastyExtraFeeCollectionFactory = \Magento\Framework\App\ObjectManager::getInstance()
                    ->get(\Amasty\Extrafee\Model\ResourceModel\ExtrafeeQuote\CollectionFactory::class);
            }
        }

        return $this->amastyExtraFeeCollectionFactory;
    }

    /**
     * Set form data
     *
     * @return $this
     */
    protected function _setFormData($data = null)
    {
        if (null === $data) {
            $data = $this->getRequest()->getParams();
        }

        if (false === $data) {
            $this->dataPersistor->clear($this->_formSessionKey);
        } else {
            $this->dataPersistor->set($this->_formSessionKey, $data);
        }

        /* deprecated save in session */
        $this->_getSession()->setData($this->_formSessionKey, $data);

        return $this;
    }

    /**
     * @param  array  $changeSections
     * @param  string $key
     * @param  string $nameOfField
     * @param  string $oldValue
     * @param  string $newValue
     * @return array
     */
    protected function writeChanges(string $sectionName, array &$changeSections, string $key, string $nameOfField, string $oldValue, string $newValue): array
    {
        $changeSections[$sectionName][$key]['name_of_field'] = $nameOfField;
        $changeSections[$sectionName][$key]['old_value'] = $oldValue;
        $changeSections[$sectionName][$key]['new_value'] = $newValue;

        return $changeSections;
    }
}
