<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Block\Adminhtml\Order;

use Magefan\OrderEdit\Block\Adminhtml\Order\Edit\Form as MainForm;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Model\OrderRepository;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Session\Quote;
use Magento\Sales\Model\AdminOrder\Create;
use Magento\Framework\Json\EncoderInterface;
use Magento\Customer\Model\Metadata\FormFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Customer\Model\Address\Mapper;
use Magento\Framework\App\Request\DataPersistorInterface;

class Form extends \Magento\Sales\Block\Adminhtml\Order\Create\Form
{
    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var mixed
     */
    protected $order;

    /**
     * Address form template
     *
     * @var string
     */
    protected $_template = 'Magefan_OrderEdit::order/view/form.phtml';

    /**
     * @param Context $context
     * @param Quote $sessionQuote
     * @param Create $orderCreate
     * @param PriceCurrencyInterface $priceCurrency
     * @param EncoderInterface $jsonEncoder
     * @param FormFactory $customerFormFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param CurrencyInterface $localeCurrency
     * @param Mapper $addressMapper
     * @param OrderRepository $orderRepository
     * @param DataPersistorInterface $dataPersistor
     * @param array $data
     */
    public function __construct(
        Context $context,
        Quote $sessionQuote,
        Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        EncoderInterface $jsonEncoder,
        FormFactory $customerFormFactory,
        CustomerRepositoryInterface $customerRepository,
        CurrencyInterface $localeCurrency,
        Mapper $addressMapper,
        OrderRepository $orderRepository,
        DataPersistorInterface $dataPersistor,
        array $data = []
    ) {
        $this->orderRepository = $orderRepository;
        $this->dataPersistor = $dataPersistor;
        parent::__construct(
            $context,
            $sessionQuote,
            $orderCreate,
            $priceCurrency,
            $jsonEncoder,
            $customerFormFactory,
            $customerRepository,
            $localeCurrency,
            $addressMapper,
            $data
        );
    }

    /**
     * @return false|\Magento\Sales\Api\Data\OrderInterface|mixed
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function getOrder()
    {
        if (null === $this->order) {
            try {
                $this->order = $this->orderRepository->get((int)$this->getRequest()->getParam('order_id'));
            } catch (NoSuchEntityException $e) {
                $this->order = false;
            }
        }

        return $this->order;
    }

    /**
     * Get save url
     *
     * @return string
     */
    public function getSaveUrl(): string
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $formType = $this->getRequest()->getParam('form_type');

        return $this->getUrl($this->getUrl('*/*/save', ['order_id' => $orderId,'form_type' => $formType]));
    }

    /**
     * Retrieve url for loading blocks
     *
     * @return string
     */
    public function getLoadBlockUrl(): string
    {
        $formType = (int)$this->getRequest()->getParam('form_type');
        return $this->getUrl('mforderedit/order/loadBlock', ['form_type' => $formType]);
    }

    /**
     * @return string
     */
    public function toHtml(): string
    {
        $formType = (int)$this->getRequest()->getParam('form_type');
        $data = (array)$this->dataPersistor->get('order_edit_form_data');

        if (!$formType) {
            return '';
        }

        if (in_array($formType, [MainForm::PAYMENT_METHOD_EDIT_FORM, MainForm::ALL_TYPES_EDIT_FORM])) {
            if (isset($data['payment']) && isset($data['payment']['method'])) {
                $this->_sessionQuote->getQuote()->getPayment()->setMethod((string)$data['payment']['method']);
            }
        }

        if (in_array($formType, [MainForm::SHIPPING_METHOD_EDIT_FORM, MainForm::ALL_TYPES_EDIT_FORM])) {
            if (isset($data['order']) && isset($data['order']['shipping_method'])) {
                $this->_sessionQuote->getQuote()->getShippingAddress()->setShippingMethod((string)$data['order']['shipping_method']);
            }
        }

        return parent::toHtml();
    }
}
