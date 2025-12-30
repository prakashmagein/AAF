<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Block\Adminhtml\Order\Edit;

use Magento\Sales\Model\OrderRepository;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     *  Order Info Edit Form
     */
    const ORDER_INFO_EDIT_FORM = 1;

    /**
     *  Account Info Edit Form
     */
    const ACCOUNT_INFO_EDIT_FORM = 2;

    /**
     *  Payment Method Edit Form
     */
    const PAYMENT_METHOD_EDIT_FORM = 3;

    /**
     *  Shipping Method Edit Form
     */
    const SHIPPING_METHOD_EDIT_FORM = 4;

    /**
     *  Items Ordered Edit Form
     */
    const ITEMS_ORDERED_EDIT_FORM = 5;

    /**
     *  All Types Edit Form
     */
    const ALL_TYPES_EDIT_FORM = 6;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var mixed
     */
    private $order;

    /**
     * @param Context $context
     * @param OrderRepository $orderRepository
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        OrderRepository $orderRepository,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    ) {
        $this->orderRepository = $orderRepository;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return false|\Magento\Sales\Api\Data\OrderInterface|mixed
     * @throws \Magento\Framework\Exception\InputException
     */
    private function getOrder()
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
     * @return Form
     * @throws \Magento\Framework\Exception\InputException
     */
    public function _prepareLayout()
    {
        $success = true;

        if (!$this->getOrder()) {
            $success = false;
        }

        if ($success) {
            $formType = (string)$this->getRequest()->getParam('form_type');
            $additionalText = __('Edit Order');

            switch ($formType) {
                case self::ORDER_INFO_EDIT_FORM:
                    $additionalText = __('Edit Order Information');
                    break;
                case self::ACCOUNT_INFO_EDIT_FORM:
                    $additionalText = __('Edit Account Information');
                    break;
                case self::PAYMENT_METHOD_EDIT_FORM:
                    $additionalText = __('Edit Payment Information');
                    break;
                case self::SHIPPING_METHOD_EDIT_FORM:
                    $additionalText = __('Edit Shipping Information');
                    break;
                case self::ITEMS_ORDERED_EDIT_FORM:
                    $additionalText = __('Edit Order Items Information');
                    break;
            }

            $this->pageConfig->getTitle()->set(__(sprintf("#%s", $this->getOrder()->getIncrementId()) . ' - ' .$additionalText));
        }

        return parent::_prepareLayout();
    }

    /**
     * @return Form
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create();
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
