<?php
declare(strict_types=1);

namespace Gwl\Datalayerevents\Block\Select;

use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\ResourceModel\Order\Payment\Collection;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class Payment extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Session
     */
    protected $checkoutSession;

    
    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentData;


    protected $serializer;

    protected $productRepository;

    protected $categoryCollectionFactory;

    protected $storeManager;

    /**
     * @param Context $context
     * @param Session $checkoutSession
     * @param Data $dataHelper
     * @param DataItem $additionalData
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Payment\Helper\Data $paymentData,
        SerializerInterface $serializer,
        ProductRepositoryInterface $productRepository,
        CollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->checkoutSession = $checkoutSession;
        $this->paymentData = $paymentData;
        $this->serializer = $serializer;
        $this->productRepository = $productRepository;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Get list item event
     *
     * @return array
     */
    public function getListItems()
    {
        return $this->getItemsPaymentShipping();
    }

    /**
     * Get coupon code
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCouponCode()
    {
        $quote = $this->checkoutSession->getQuote();
        if ($quote->getCouponCode()) {
            return $quote->getCouponCode();
        }
        return '';
    }

    /**
     * Get value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->checkoutSession->getQuote()->getGrandTotal();
    }

    /**
     * Get currency
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrency()
    {
        return $this->dataHelper->getCurrency();
    }

    /**
     * Serialize item
     *
     * @param array $item
     * @return bool|string
     */
    public function serializeItem($item)
    {
        return $this->serializer->serialize($item);
    }

    /**
     * Get payment Title
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPaymentType()
    {
        try {
            if ($this->getRequest()->getParam('method')) {
                $paymentList = $this->paymentData->getPaymentMethodList();
               // return $paymentList[$this->getRequest()->getParam('method')];
            }
            return $this->getRequest()->getParam('method');
            return $this->checkoutSession->getQuote()->getPayment()->getMethodInstance()->getTitle();
        } catch (\Exception $ex) {
            return '';
        }
    }

    /**
     * Escaper.
     *
     * @return \Magento\Framework\Escaper
     */
    public function escaper()
    {
        return $this->_escaper;
    }

    public function getItemsPaymentShipping()
    {
        $data = [];
        if ($items = $this->checkoutSession->getQuote()->getItems()) {
            foreach ($items as $key => $item) {

            $product = $this->productRepository->getById($item->getProductId());

            $categoryIds = $product->getCategoryIds();
            $categoryCollection = $this->categoryCollectionFactory->create()
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('entity_id', ['in' => $categoryIds]);
            $categoryNames = [];
            foreach ($categoryCollection as $category) {
                $categoryNames[] = $category->getName();
            }

            $data[] = [
                'item_id' => $product->getSku(),
                'item_name' => $product->getName(),
                'original_price' => (float) $product->getPrice(),
                'final_price' => (float) $item->getPrice(),
                'discount' => (float) ($product->getPrice() - $item->getPrice()),
                'quantity' => (int) $item->getQty(),
                'currency' => $this->checkoutSession->getQuote()->getQuoteCurrencyCode(),
                'item_category' => $categoryNames,
                'item_list_name' => '',
                'item_list_id' => '',
                'item_brand' => '',
                'item_url' => $product->getProductUrl()
            ];
            }
        }
        return $data;
    }

    public function getVariantConfigurable($item)
    {
        if ($item->getCustomOptions() && isset($item->getCustomOptions()['attributes'])) {
            $optionsSelected = $item->getCustomOptions()['attributes']->getValue();
            $allOption = $item->getTypeInstance()->getConfigurableAttributesAsArray($item);
            if ($optionsSelected) {
                // $optionsSelected = $this->data->unSerializeItem($optionsSelected);
                $variant = [];
                foreach ($optionsSelected as $key => $optionValue) {
                    $option = $allOption[$key];
                    foreach ($allOption[$key]['values'] as $value) {
                        if ($value['value_index'] == $optionValue) {
                            $variant[] = $option['store_label'] . ': ' . $value['store_label'];
                        }
                    }
                }
                if ($variant) {
                    return implode(',', $variant);
                }
            }
        }
        return '';
    }

    public function getCurrencyCode()
    {
        $storeId = $this->storeManager->getStore()->getId();
        $store = $this->storeManager->getStore($storeId);
        return $store->getCurrentCurrencyCode();
    }



    public function getAppliedGiftCards()
    {
        $quote = $this->checkoutSession->getQuote();
        $giftCards = $quote->getGiftCards(); // Gift card data is stored as JSON

        if ($giftCards) {
            return json_decode($giftCards, true); // Convert to an array
        }

        return '';
    }
}
