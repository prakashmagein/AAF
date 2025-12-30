<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\OrderEdit\Observer\Backend\Layout;

use Magefan\OrderEdit\Block\Adminhtml\Order\Edit\Form;
use Magento\Framework\App\RequestInterface;
use Magento\Backend\Model\Session\Quote as SessionQuote;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\Order;
use Magento\Framework\Locale\Resolver as LocaleResolver;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\ShippingMethodManagement;
use Magefan\OrderEdit\Model\Quote\Manager as QuoteManager;

class LoadBefore implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var SessionQuote
     */
    protected $sessionQuote;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var LocaleResolver
     */
    protected $localeResolver;

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var ShippingMethodManagement
     */
    protected $shippingMethodManagement;

    /**
     * @var QuoteManager
     */
    protected $quoteManager;

    /**
     * @param RequestInterface $request
     * @param SessionQuote $sessionQuote
     * @param OrderRepository $orderRepository
     * @param LocaleResolver $localeResolver
     * @param CartRepositoryInterface|null $quoteRepository
     * @param ShippingMethodManagement|null $shippingMethodManagement
     * @param QuoteManager|null $quoteManager
     */
    public function __construct(
        RequestInterface $request,
        SessionQuote $sessionQuote,
        OrderRepository $orderRepository,
        LocaleResolver $localeResolver,
        CartRepositoryInterface $quoteRepository = null,
        ShippingMethodManagement $shippingMethodManagement = null,
        QuoteManager $quoteManager = null
    ) {
        $this->request = $request;
        $this->sessionQuote = $sessionQuote;
        $this->orderRepository = $orderRepository;
        $this->localeResolver = $localeResolver;
        $this->quoteRepository = $quoteRepository ?:  \Magento\Framework\App\ObjectManager::getInstance()
            ->get(CartRepositoryInterface::class);
        $this->shippingMethodManagement = $shippingMethodManagement ?:  \Magento\Framework\App\ObjectManager::getInstance()
            ->get(ShippingMethodManagement::class);
        $this->quoteManager = $quoteManager ?:  \Magento\Framework\App\ObjectManager::getInstance()
            ->get(QuoteManager::class);
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($observer->getFullActionName() == 'mforderedit_order_edit') {
            $formType = (int)$this->request->getParam('form_type');

            try {
                $order = $this->orderRepository->get((int)$this->request->getParam('order_id'));
                $this->setSessionQuote($order);
            } catch (NoSuchEntityException $e) {
                return;
            }

            switch ($formType) {
                case Form::ALL_TYPES_EDIT_FORM:
                    $observer->getLayout()->getUpdate()->addHandle('mforder_quick_edit_form');
                    break;
                case Form::ITEMS_ORDERED_EDIT_FORM:
                    $observer->getLayout()->getUpdate()->addHandle('mforder_product_form');
                    break;
                case Form::SHIPPING_METHOD_EDIT_FORM:
                    //When order is placed from frontend,not all rates are represented in shipping edit page
                    $this->addAllPossibleRatesToQuote((int)$order->getQuoteId());

                    $observer->getLayout()->getUpdate()->addHandle('mforder_shipping_form');
                    break;
                case Form::PAYMENT_METHOD_EDIT_FORM:
                    $observer->getLayout()->getUpdate()->addHandle('mforder_payment_form');
                    break;
                case Form::ORDER_INFO_EDIT_FORM:
                    $observer->getLayout()->getUpdate()->addHandle('mforder_order_info_edit_form');
                    break;
                case Form::ACCOUNT_INFO_EDIT_FORM:
                    $observer->getLayout()->getUpdate()->addHandle('mforder_account_info_edit_form');
                    break;
            }
        }
    }

    /**
     * @param int $quoteId
     * @return void
     */
    protected function addAllPossibleRatesToQuote(int $quoteId): bool
    {
        try {
            $quote = $this->quoteRepository->get($quoteId);
        } catch (NoSuchEntityException $e) {
            return false;
        }

        $quote->setIsActive(true);
        $shippingMethodsForQuote = $this->shippingMethodManagement
            ->estimateByExtendedAddress((int)$quote->getId(), $quote->getShippingAddress());
        $quote->setIsActive(false);

        $quoteRates = $quote->getShippingAddress()->getGroupedAllShippingRates();

        if (!$this->isCountable($quoteRates) || !$this->isCountable($shippingMethodsForQuote)) {
            return false;
        }

        if (count($quoteRates) < count($shippingMethodsForQuote)) {
            \Magento\Framework\App\ObjectManager::getInstance()->get(\Magefan\OrderEdit\Controller\Adminhtml\Order\LoadBlock::class)->resetShipping();
            return true;
        }

        return false;
    }

    /**
     * @param mixed $quoteId
     * @return bool
     */
    private function isCountable($var): bool
    {
        return (is_array($var) || $var instanceof Countable);
    }

    /**
     * @param Order $order
     * @return void
     */
    private function setSessionQuote(Order $order)
    {
        $this->sessionQuote->setOrderId($order->getId());
        $this->sessionQuote->setCurrencyId($order->getOrderCurrencyCode());
        $this->sessionQuote->setCustomerId($order->getCustomerId() ?: false);
        $this->sessionQuote->setStoreId($order->getStoreId());
        $this->sessionQuote->setQuoteId($order->getQuoteId());
        $this->sessionQuote->setData('locale', $this->localeResolver->getLocale());

        $this->setTaxRateIdIfNull();
    }

    /**
     * @return void
     */
    private function setTaxRateIdIfNull(): void
    {
        $quote = $this->sessionQuote->getQuote();
        if (is_object($quote)) {
            $taxRateId = $quote->getData('mf_tax_rate_id');
            if (is_null($taxRateId)) {
                $taxRateId = $this->quoteManager->getTaxRateIdFromQuote();
                $quote->setData('mf_tax_rate_id', $taxRateId);

                try {
                    $this->quoteRepository->save($quote);
                } catch (NoSuchEntityException $e) {
                }
            }
        }
    }
}
