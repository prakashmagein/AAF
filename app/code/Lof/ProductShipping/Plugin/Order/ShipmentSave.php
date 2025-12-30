<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_ProductShipping
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://www.landofcoder.com/LICENSE-1.0.html
 */
declare(strict_types=1);

namespace Lof\ProductShipping\Plugin\Order;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Shipping\Controller\Adminhtml\Order\Shipment\Save as SaveController;
use Magento\Framework\Registry;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Helper\Data as SalesData;
use Magento\Sales\Model\Order\Shipment\Validation\QuantityValidator;
use Lof\ProductShipping\Model\Carrier;
use Magento\Quote\Model\ResourceModel\Quote\Address\Rate\CollectionFactory as RateCollectionFactory;

/**
 * Controller for generation of new Shipments from Backend
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ShipmentSave
{
    /**
     * @var \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader
     */
    protected $shipmentLoader;

    /**
     * @var \Magento\Shipping\Model\Shipping\LabelGenerator
     */
    protected $labelGenerator;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\ShipmentSender
     */
    protected $shipmentSender;

    /**
     * @var \Magento\Sales\Model\Order\Shipment\ShipmentValidatorInterface
     */
    private $shipmentValidator;

    /**
     * @var SalesData
     */
    private $salesData;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Lof\ProductShipping\Model\ShippingFactory
     */
    protected $shippingFactory;

    /**
     * @var \Lof\ProductShipping\Model\ShippingmethodFactory
     */
    protected $shippingMethodFactory;

    /**
     * @var RateCollectionFactory
     */
    protected $rateCollectionFactory;

    /**
     * @var \Magento\Sales\Model\Order\ItemFactory
     */
    protected $orderItemFactory;

    /**
     * @var \Lof\ProductShipping\Helper\Data
     */
    protected $helperData;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var \Magento\Backend\Model\View\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $_formKeyValidator;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Sales\Model\Order[]
     */
    protected $_orderData = [];

    /**
     * @var int[]
     */
    protected $_productShippingMethodIds = [];

    /**
     * @var SaveController|null
     */
    protected $_currentObject = null;

    /**
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader
     * @param \Magento\Shipping\Model\Shipping\LabelGenerator $labelGenerator
     * @param \Magento\Sales\Model\Order\Email\Sender\ShipmentSender $shipmentSender
     * @param \Lof\ProductShipping\Model\ShippingFactory $shippingFactory
     * @param \Lof\ProductShipping\Model\ShippingmethodFactory $shippingMethodFactory
     * @param \Lof\ProductShipping\Helper\Data $helperData
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Sales\Model\Order\ItemFactory $orderItemFactory
     * @param RateCollectionFactory $rateCollectionFactory
     * @param Registry $registry
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Sales\Model\Order\Shipment\ShipmentValidatorInterface|null $shipmentValidator
     * @param SalesData $salesData
     */
    public function __construct(
        \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader,
        \Magento\Shipping\Model\Shipping\LabelGenerator $labelGenerator,
        \Magento\Sales\Model\Order\Email\Sender\ShipmentSender $shipmentSender,
        \Lof\ProductShipping\Model\ShippingFactory $shippingFactory,
        \Lof\ProductShipping\Model\ShippingmethodFactory $shippingMethodFactory,
        \Lof\ProductShipping\Helper\Data $helperData,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\Order\ItemFactory $orderItemFactory,
        RateCollectionFactory $rateCollectionFactory,
        Registry $registry,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Sales\Model\Order\Shipment\ShipmentValidatorInterface $shipmentValidator = null,
        SalesData $salesData = null
    ) {
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->shipmentLoader = $shipmentLoader;
        $this->labelGenerator = $labelGenerator;
        $this->shipmentSender = $shipmentSender;
        $this->shippingFactory = $shippingFactory;
        $this->orderFactory = $orderFactory;
        $this->orderItemFactory = $orderItemFactory;
        $this->rateCollectionFactory = $rateCollectionFactory;
        $this->shippingMethodFactory = $shippingMethodFactory;
        $this->helperData = $helperData;
        $this->registry = $registry;
        $this->_formKeyValidator = $formKeyValidator;
        $this->_objectManager = $objectManager;
        $this->messageManager = $messageManager;
        $this->shipmentValidator = $shipmentValidator ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Sales\Model\Order\Shipment\ShipmentValidatorInterface::class);
        $this->salesData = $salesData ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(SalesData::class);
    }

    /**
     * Get order
     *
     * @param int $orderId
     * @return \Magento\Sales\Model\Order
     */
    protected function getOrder(int $orderId): \Magento\Sales\Model\Order
    {
        if (!isset($this->_orderData[$orderId])) {
            $this->_orderData[$orderId] = $this->orderFactory->create()->load($orderId);
        }
        return $this->_orderData[$orderId];
    }
    /**
     * Save shipment and order in one transaction
     *
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @return $this
     */
    protected function _saveShipment($shipment)
    {
        $shipment->getOrder()->setIsInProcess(true);
        $transaction = $this->_objectManager->create(
            \Magento\Framework\DB\Transaction::class
        );
        $transaction->addObject(
            $shipment
        )->addObject(
            $shipment->getOrder()
        )->save();

        return $this;
    }

    /**
     * get post request
     *
     * @return \Magento\Framework\App\RequestInterface|null
     */
    public function getRequest()
    {
        return $this->_object ? $this->_object->getRequest() : null;
    }

    /**
     * Before Save shipment
     *
     * We can save only new shipment. Existing shipments are not editable
     *
     * @param SaveController $object
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function beforeExecute(
        SaveController $object
    ) {
        if ($this->helperData->getIsActive() && $this->helperData->isSplitShipment()) {
            $this->_object = $object;
            $orderId = $this->getRequest()->getParam('order_id');
            $productShippingName = Carrier::CODE."_".Carrier::CODE;
            $orderData = $this->getOrder((int)$orderId);
            if ($productShippingName == $orderData->getShippingMethod()) {
                $posts = $this->getRequest()->getParam('shipment');
                $splitItems = $this->initProductShipping($orderId, $posts);
                if (count($splitItems) > 0) {
                    return $this->executeSplitShipment($splitItems);
                }
            }
        }
    }

    /**
     * @param SaveController $subject
     * @param callable $proceed
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function aroundExecute(
        SaveController $subject,
        callable $proceed
    ) {
        if (!$this->helperData->getIsActive() || !$this->helperData->isSplitShipment()) {
            return $proceed();
        }
        $this->_object = $subject;
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $orderId = $this->getRequest()->getParam('order_id');
        $orderModel = $this->getOrder((int)$orderId);
        if (!$orderModel->canShip()) {
            return $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
        }
        return $proceed();
    }

    /**
     * Save shipment
     *
     * We can save only new shipment. Existing shipments are not editable
     *
     * @param mixed|array $splitItems
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function executeSplitShipment($splitItems = [])
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $formKeyIsValid = $this->_formKeyValidator->validate($this->getRequest());
        $isPost = $this->getRequest()->isPost();
        if (!$formKeyIsValid || !$isPost) {
            $this->messageManager->addErrorMessage(__('We can\'t save the shipment right now.'));
            $this->registry->registry("redirect_url", 'sales_order_index');
            return $resultRedirect->setPath('sales/order/index');
        }

        $orderId = $this->getRequest()->getParam('order_id');
        $data = $this->getRequest()->getParam('shipment');

        if (!empty($data['comment_text'])) {
            $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setCommentText($data['comment_text']);
        }

        $isNeedCreateLabel = isset($data['create_shipping_label']) && $data['create_shipping_label'];
        $responseAjax = new \Magento\Framework\DataObject();

        try {
            $shipmentId = $this->getRequest()->getParam('shipment_id');
            $tracking = $this->getRequest()->getParam('tracking');
            $totals = count($splitItems);
            $index = 1;
            foreach ($splitItems as $shippingId => $shippingItems) {
                $newData = $data;
                $newData["items"] = $shippingItems["items"];
                $checked = $this->generateShipment(
                    $orderId,
                    $shipmentId,
                    $tracking,
                    $newData,
                    $isNeedCreateLabel,
                    $responseAjax,
                    $shippingItems["method"],
                    $shippingItems["rate"]
                );
                if ($checked && $checked != true) {
                    $responseAjax = $checked;
                }
                if ($index < $totals ) {
                    $this->registry->unregister("current_shipment");
                }
                $index++;
            }

            $this->_objectManager->get(\Magento\Backend\Model\Session::class)->getCommentText(true);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($isNeedCreateLabel) {
                $responseAjax->setError(true);
                $responseAjax->setMessage($e->getMessage());
            } else {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->registry->registry("redirect_url", 'order_shipment_new');
                return $resultRedirect->setPath('order_shipment/new', ['order_id' => $orderId]);
            }
        } catch (\Exception $e) {
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
            if ($isNeedCreateLabel) {
                $responseAjax->setError(true);
                $responseAjax->setMessage(__('An error occurred while creating shipping label.'));
            } else {
                $this->messageManager->addErrorMessage(__('Cannot save shipment.'));
                $this->registry->registry("redirect_url", 'order_shipment_new');
                return $resultRedirect->setPath('order_shipment/new', ['order_id' => $orderId]);
            }
        }
        if ($isNeedCreateLabel) {
            return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setJsonData($responseAjax->toJson());
        }
        $this->registry->registry("redirect_url", 'sales_order_view');
        return $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
    }

    /**
     * generate shipment
     *
     * @param int $orderId
     * @param string|int $shipmentId
     * @param mixed $tracking
     * @param mixed $data
     * @param bool|int|null $isNeedCreateLabel
     * @param mixed|null $responseAjax
     * @param string $method
     * @param int $method
     * @param int|float $rate
     * @return mixed|bool|\Magento\Framework\DataObject|null
     */
    public function generateShipment(
        $orderId,
        $shipmentId = 0,
        $tracking = [],
        $data = [],
        $isNeedCreateLabel = false,
        $responseAjax = null,
        $method = "",
        $rate = 0
    )
    {
        $flag = false;
        try {
            $this->shipmentLoader->setOrderId($orderId);
            $this->shipmentLoader->setShipmentId($shipmentId);
            $this->shipmentLoader->setShipment($data);
            $this->shipmentLoader->setTracking($tracking);
            $shipment = $this->shipmentLoader->load();
            if (!$shipment) {
                return $flag;
            }
            $productShipping = "";
            if ($method) {
                $productShipping = __("Shipment for method %1 with rate = %2", $method, $rate);
                $data['comment_text'] .= "\n";
                $data['comment_text'] .= $productShipping;
            }

            if (!empty($data['comment_text'])) {
                $shipment->addComment(
                    $data['comment_text'],
                    isset($data['comment_customer_notify']),
                    isset($data['is_visible_on_front'])
                );

                $shipment->setCustomerNote($data['comment_text']);
                $shipment->setCustomerNoteNotify(isset($data['comment_customer_notify']));
            }
            $validationResult = $this->shipmentValidator->validate($shipment, [QuantityValidator::class]);

            if ($validationResult->hasMessages()) {
                $this->messageManager->addErrorMessage(
                    __("Shipment Document Validation Error(s):\n" . implode("\n", $validationResult->getMessages()))
                );
                return $flag;
            }
            $shipment->register();

            $shipment->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));

            if ($isNeedCreateLabel) {
                $this->labelGenerator->create($shipment, $this->_request);
                $responseAjax->setOk(true);
            }

            $shipment->setData("product_shipping_method", $method);
            $shipment->setData("product_shipping_rate", (float)$rate);

            $this->_saveShipment($shipment);

            if (!empty($data['send_email']) && $this->salesData->canSendNewShipmentEmail()) {
                $this->shipmentSender->send($shipment);
            }

            $shipmentCreatedMessage = __('The shipment has been created.');
            $labelCreatedMessage = __('You created the shipping label.');

            $this->messageManager->addSuccessMessage(
                $isNeedCreateLabel ? $shipmentCreatedMessage. ' ' .$productShipping. ' ' . $labelCreatedMessage : $shipmentCreatedMessage
            );
            $this->_objectManager->get(\Magento\Backend\Model\Session::class)->getCommentText(true);
            $flag = true;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($isNeedCreateLabel) {
                $responseAjax->setError(true);
                $responseAjax->setMessage($e->getMessage());
            } else {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $flag;
            }
        } catch (\Exception $e) {
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
            if ($isNeedCreateLabel) {
                $responseAjax->setError(true);
                $responseAjax->setMessage(__('An error occurred while creating shipping label.'));
            } else {
                return $flag;
            }
        }
        if ($isNeedCreateLabel) {
            return $responseAjax;
        }

        return $flag;
    }

    /**
     * Init
     *
     * @param int $orderId
     * @param mixed $data
     * @return mixed
     */
    public function initProductShipping($orderId, $data = [])
    {
        $splits = [];
        $order = $this->getOrder((int)$orderId);
        $productShippingIds = $this->getProductShippingAddress($order);
        $itemQty = isset($data['items']) ? $data['items'] : [];
        foreach ($itemQty as $itemId => $quantity) {
            $orderItem = $this->orderItemFactory->create()->load($itemId);
            $parentId = $orderItem->getParentItemId();
            $productId = $orderItem->getProductId();

            $foundRecord = $this->shippingFactory->create()->getCollection()
                                ->addProductToFilter($productId)
                                ->addFieldToFilter("lofshipping_id", ["in" => $productShippingIds])
                                ->getFirstItem();
            if ($foundRecord && $foundRecord->getId()) {
                if (!isset($splits[$foundRecord->getId()])) {
                    $splits[$foundRecord->getId()] = [];
                    $splits[$foundRecord->getId()]["method"] = $this->getShippingMethodName((int)$foundRecord->getShippingMethodId());
                    $splits[$foundRecord->getId()]["rate"] = $this->calculateShippingRate($foundRecord, $quantity);
                    $splits[$foundRecord->getId()]["items"] = [];
                }
                $splits[$foundRecord->getId()]["items"][$itemId] = $quantity;
            } else {
                if (!isset($splits["default"])) {
                    $splits["default"] = [];
                    $splits["default"]["method"] = "";
                    $splits["default"]["rate"] = 0;
                    $splits["default"]["items"] = [];
                }
                $splits["default"]["items"][$itemId] = $quantity;
            }
        }
        return $splits;
    }

    /**
     * Get product shipping address
     *
     * @param mixed $order
     * @return mixed
     */
    public function getProductShippingAddress($order)
    {
        if (count($this->_productShippingMethodIds) <=0 ) {
            $address = $order->getShippingAddress();
            $quoteRate = $this->rateCollectionFactory->create()
                                ->addFieldToFilter("address_id", $address->getQuoteAddressId())
                                ->addFieldToFilter("carrier", Carrier::CODE)
                                ->getFirstItem();
            if ($quoteRate && $quoteRate->getMethodDescription()) {
                $methodDescription = $quoteRate->getMethodDescription();
                $methodDescription = str_replace("methods:", "", $methodDescription);
                $this->_productShippingMethodIds = $methodDescription ? explode("|", $methodDescription) : [];
            }
        }
        return $this->_productShippingMethodIds;
    }

    /**
     * get shipping method name
     *
     * @param int $shippingMethodId
     * @return string
     */
    public function getShippingMethodName($shippingMethodId)
    {
        if ($shippingMethodId) {
            $method = $this->shippingMethodFactory->create()->load($shippingMethodId);
            return $method->getMethodName();
        }
        return "";
    }

    /**
     * Calculate shipping rate for product
     *
     * @param \Lof\ProductShipping\Model\Shipping|mixed
     * @param int $quantity
     * @return float|int
     */
    public function calculateShippingRate($shippingRate, $quantity = 0)
    {
        $rate = 0;
        if ($quantity) {
            $rate = (float)$shippingRate->getPrice();
            if ($shippingRate->getPriceForUnit()) {
                $secondPrice = (float)$shippingRate->getSecondPrice();
                if ($shippingRate->getAllowSecondPrice() && $secondPrice > 0 && $quantity > 1) {
                    $rate += $secondPrice*($quantity-1);
                } else {
                    $rate = $rate * $quantity;
                }
            }
        }
        return $rate;
    }

}
