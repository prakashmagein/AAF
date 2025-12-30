<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Block\Adminhtml\Order\AccountInfo;

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
use Magento\Customer\Model\Customer\DataProviderWithDefaultAddresses;
use Magefan\OrderEdit\Block\Adminhtml\Order\Edit\Form;

class Edit extends \Magefan\OrderEdit\Block\Adminhtml\Order\Form
{
    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var DataProviderWithDefaultAddresses
     */
    private $dataProviderWithDefaultAddresses;

    /**
     * Address form template
     *
     * @var string
     */
    protected $_template = 'Magefan_OrderEdit::order/view/accountInfoEdit.phtml';

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
     * @param DataProviderWithDefaultAddresses $dataProviderWithDefaultAddresses
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
        DataProviderWithDefaultAddresses $dataProviderWithDefaultAddresses,
        FormFactory $formFactory,
        array $data = []
    ) {
        $this->dataProviderWithDefaultAddresses = $dataProviderWithDefaultAddresses;
        $this->formFactory = $formFactory;
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $jsonEncoder, $customerFormFactory, $customerRepository, $localeCurrency, $addressMapper, $orderRepository, $dataPersistor, $data);
    }

    /**
     * @return string
     */
    public function getJs(): string
    {
        $url = $this->getUrl('mforderedit/grid/grid');
        $js = "require([
                 'jquery',
                 'Magento_Ui/js/modal/modal'
                  ], function($, alert) {
              $(document).ready(function(){

              if($('#gridfield').children().length === 0){
                var options = {
                      type: 'popup',
                      responsive: true,
                      innerScroll: true,
                       buttons: [{
                           text: 'Continue',
                           class: 'action-default primary add',
                           click: function () {
                               var tr = $('input:checked').parentsUntil('tbody');
                               if (tr.length) {
                                  var arr = {};
                                  tr.children('td').each(function() {
                                    var className = this.className.substring(this.className.indexOf('col-')).replace(/\s/g,'');
                                    arr[className] = this.textContent.replace(/\s/g,'');
                                  });
                                  
                                  for (var key in arr ) {
                                     $('#'+key).val(arr[key]);
                                  }
                               }
                               this.closeModal();
                           }
                      }]
                      };

                 var curl = '" . $url . "';
                 $.ajax({
                   url: curl,
                   type: 'GET',
                   success: function(data) {
                      var result = $(data).find('#order_edit_base_fieldset_grid');
                      $('#gridfield').html(result.html()).modal(options).modal('openModal');
                   },
                   error: function(xhr, status, errorThrown) {
                      console.log('Error happens. Try again.');
                   },
                    complete: function (xhr, status) {
                      //$('#showresults').slideDown('slow')
                     }
                 });
              }
              else {
                 $('#gridfield').modal('openModal');
              }
              });
         }
        );";
        return $js;
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

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['class' => 'fieldset-wide']
        );


        $order = $this->getOrder();

        if (!$order) {
            return '';
        }

        $typesOfFields= [];
        $valuesOfFields = [];
        $nameOfFields = [];
        $labelsOfFields = [];
        $required= [];
        $optionsOfFields = [];

        if (in_array($formType, [Form::ACCOUNT_INFO_EDIT_FORM, Form::ALL_TYPES_EDIT_FORM])) {
            $meta = $this->dataProviderWithDefaultAddresses->getMeta();
            $need = [
                'prefix',
                'firstname',
                'lastname',
                'middlename',
                'suffix',
                'email',
                'website_id',
                'group_id',
                'dob',
                'gender',
                'taxvat',
                'disable_auto_group_change'
            ];

            foreach ($need as $n) {
                $customerData[$n] = (string)$order->getData('customer_'.$n);
            }

            $fieldset->addField('registered', 'button', [
                'label' => __('Click to change customer'),
                'value' => ('Change Customer'),
                'name' => 'registered',
                'class' => 'action-basic',
                'onclick' => $this->getJs(),
            ]);

            foreach ($customerData as $key => $value) {
                if (in_array($key, array_values($need))) {
                    $subKey = 'col-'.$key;
                    $configArray = $meta['customer']['children'][(string)$key]['arguments']['data']['config'];
                    $valuesOfFields[$subKey] = $data[$subKey] ?? $value;
                    $typesOfFields[$subKey] = $configArray['dataType'];

                    if ($typesOfFields[$subKey] === 'boolean') {
                        $typesOfFields[$subKey] = 'checkbox';
                    }

                    $nameOfFields[$subKey] = $subKey;
                    $labelsOfFields[$subKey] = $configArray['label'];
                    $required[$subKey] = (bool)$configArray['required'];

                    if (in_array('options', array_keys($configArray))) {
                        $optionsOfFields[$subKey] = $configArray['options'];
                    }
                }
            }

        }

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
