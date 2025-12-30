<?php
/**
 * PaymentMethods
 *
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */
namespace Magepow\OnestepCheckout\Model\System\Config\Source;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Option\ArrayInterface;
use Magento\Payment\Model\Method\Factory;
use Magento\Store\Model\ScopeInterface;
use Magepow\OnestepCheckout\Helper\Data;


class PaymentMethods implements ArrayInterface
{
    /**
     * @type \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @type \Magento\Payment\Model\Method\Factory
     */
    protected $_paymentMethodFactory;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * PaymentMethods constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param Factory $paymentMethodFactory
     * @param Data $dataHelper
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Factory $paymentMethodFactory,
        Data $dataHelper
    )
    {
        $this->_scopeConfig          = $scopeConfig;
        $this->_paymentMethodFactory = $paymentMethodFactory;
        $this->dataHelper            = $dataHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $options = [['label' => __('-- Please select --'), 'value' => '']];

        $payments = $this->getActiveMethods();
        foreach ($payments as $paymentCode => $paymentModel) {
            $options[$paymentCode] = [
                'label' => $paymentModel->getTitle(),
                'value' => $paymentCode
            ];
        }

        return $options;
    }

    /**
     * Get all active payment method
     *
     * @return array
     */
    public function getActiveMethods()
    {
        $methods       = [];
        $paymentConfig = $this->_scopeConfig->getValue('payment', ScopeInterface::SCOPE_STORE, null);
        if ($this->dataHelper->isEnabledMultiSafepay()) {
            $paymentConfig = array_merge(
                $this->_scopeConfig->getValue('payment', ScopeInterface::SCOPE_STORE, null),
                $this->_scopeConfig->getValue('gateways', ScopeInterface::SCOPE_STORE, null)
            );
        }

        foreach ($paymentConfig as $code => $data) {
            if (isset($data['active'], $data['model']) && (bool)$data['active']) {
                try {
                    $methodModel = $this->_paymentMethodFactory->create($data['model']);
                    if (is_object($methodModel)) {
                        $methodModel->setStore(null);
                        if ($methodModel->getConfigData('active', null)) {
                            $methods[$code] = $methodModel;
                        }
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        return $methods;
    }
}
