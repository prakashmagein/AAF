<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Block\Adminhtml\Order\OrderInfo;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Session\Quote as SessionQuote;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Address\Mapper as AddressMapper;
use Magento\Customer\Model\Metadata\FormFactory as CustomerFormFactory;
use Magento\Framework\Json\EncoderInterface as JsonEncoder;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Locale\CurrencyInterface as LocaleCurrency;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Model\AdminOrder\Create;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\Config\Source\Order\Status;
use Magento\Cms\Ui\Component\Listing\Column\Cms\Options  as StoreOptions;
use Magento\Store\Model\StoreManagerInterface;
use Magefan\OrderEdit\Block\Adminhtml\Order\Edit\Form;

class Edit extends \Magefan\OrderEdit\Block\Adminhtml\Order\Form
{
    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var Status
     */
    private $statusModel;

    /**
     * @var StoreOptions
     */
    private $storeOptions;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Address form template
     *
     * @var string
     */
    protected $_template = 'Magefan_OrderEdit::order/view/orderInfoEdit.phtml';

    /**
     * @param Context $context
     * @param SessionQuote $sessionQuote
     * @param Create $orderCreate
     * @param PriceCurrencyInterface $priceCurrency
     * @param JsonEncoder $jsonEncoder
     * @param CustomerFormFactory $customerFormFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param LocaleCurrency $localeCurrency
     * @param AddressMapper $addressMapper
     * @param OrderRepository $orderRepository
     * @param DataPersistorInterface $dataPersistor
     * @param Status $statusModel
     * @param StoreOptions $storeOptions
     * @param StoreManagerInterface $storeManager
     * @param FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        SessionQuote $sessionQuote,
        Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        JsonEncoder $jsonEncoder,
        CustomerFormFactory $customerFormFactory,
        CustomerRepositoryInterface $customerRepository,
        LocaleCurrency $localeCurrency,
        AddressMapper $addressMapper,
        OrderRepository $orderRepository,
        DataPersistorInterface $dataPersistor,
        Status $statusModel,
        StoreOptions $storeOptions,
        StoreManagerInterface $storeManager,
        FormFactory $formFactory,
        array $data = []
    ) {
        $this->statusModel = $statusModel;
        $this->storeOptions = $storeOptions;
        $this->storeManager = $storeManager;
        $this->formFactory = $formFactory;
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
            $orderRepository,
            $dataPersistor,
            $data
        );
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFieldsetHtml(): string
    {
        $formType = (int)$this->getRequest()->getParam('form_type');
        $data = (array)$this->dataPersistor->get('order_edit_form_data');

        if (!empty($data)) {
            $this->dataPersistor->clear('order_edit_form_data');
        }

        $form = $this->formFactory->create([
            'data' => [
                'id' => 'order_edit_form',
                'action' => 'action',
                'method' => 'post',
                'enctype' => 'multipart/form-data',
            ]
        ]);

        $order = $this->getOrder();

        if (!$formType || !$order) {
            return '';
        }

        $typesOfFields= [];
        $valuesOfFields = [];
        $nameOfFields = [];
        $labelsOfFields = [];
        $required= [];
        $optionsOfFields = [];

        if (in_array($formType, [Form::ORDER_INFO_EDIT_FORM, Form::ALL_TYPES_EDIT_FORM])) {
            $valuesOfFields['increment_id'] = (string)($data['increment_id'] ?? $order->getIncrementId());
            $typesOfFields['increment_id'] = 'text';
            $nameOfFields['increment_id'] = 'increment_id';
            $labelsOfFields['increment_id'] = 'Order Number';
            $required['increment_id'] = true;

            $valuesOfFields['status'] = (string)($data['status'] ?? $order->getStatus());
            $typesOfFields['status'] = 'select';
            $nameOfFields['status'] = 'status';
            $labelsOfFields['status'] = 'Status';
            $required['status'] = true;
            $statuses = $this->statusModel->toOptionArray();
            $options = [];
            array_shift($statuses);

            foreach ($statuses as $status) {
                if (isset($status['value']) && isset($status['label'])) {
                    $options[] = ['value' => $status['value'], 'label' => $status['label']];
                }
            }

            $optionsOfFields['status'] = $options;

            $valuesOfFields['purchased_from'] = (string)($data['purchased_from'] ?? $order->getStoreId());

            $typesOfFields['purchased_from'] = 'select';
            $nameOfFields['purchased_from'] = 'purchased_from';
            $labelsOfFields['purchased_from'] = 'Purchased From';
            $required['purchased_from'] = true;
            $websites = $this->storeOptions->toOptionArray();
            $options = [];

            array_shift($websites);
            foreach ($websites as $website) {
                if (isset($website['value']) && isset($website['label'])) {
                    $options[] = ['value' => $website['value'], 'label' => $website['label']];
                }
            }

            $optionsOfFields['purchased_from'] = $options;

            $timezone = (string)$this->storeManager->getStore()->getConfig('general/locale/timezone');
            $gmtDate = date_create($order->getCreatedAt(), timezone_open('GMT'));
            $date = date_timezone_set($gmtDate, timezone_open($timezone));
            $createdAt = $date->format('Y-m-d H:i:s');
            $valuesOfFields['created_at'] = $data['order_date'] ?? $createdAt;
            $typesOfFields['created_at'] = 'date';
            $nameOfFields['created_at'] = 'order_date';
            $labelsOfFields['created_at'] = 'Order Date';
            $required['created_at'] = true;
        }

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['class' => 'fieldset-wide']
        );

        foreach ($valuesOfFields as $key => $value) {
            $fieldArray = [
                'name' => $nameOfFields[$key],
                'label' => $labelsOfFields[$key],
                'required' => $required[$key],
                'values' => in_array($key, array_keys($optionsOfFields)) ? $optionsOfFields[$key] : ''
            ];

            if ($typesOfFields[$key] === 'date') {
                $fieldArray['date_format'] = 'yyyy-MM-dd';
                $fieldArray['time_format'] = 'HH:mm:ss';
            }

            $field = $fieldset->addField(
                $key,
                $typesOfFields[$key],
                $fieldArray
            );

            if (!$field->getValue()) {
                $field->setValue($value);
            }
        }

        return $fieldset->toHtml();
    }
}
