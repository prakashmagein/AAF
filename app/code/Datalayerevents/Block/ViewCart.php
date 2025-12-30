<?php
declare(strict_types=1);

namespace Gwl\Datalayerevents\Block;


use Magento\Catalog\Helper\Product\Configuration;
use Magento\Checkout\Block\Cart\Crosssell;
use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;

class ViewCart extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Session
     */
    protected $checkoutSession;


    /**
     * @var DataItem
     */
    protected $additionalData;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var Crosssell
     */
    protected $crosssell;

    protected $productRepository;

    /**
     * @param Context $context
     * @param Session $checkoutSession
     * @param Data $dataHelper
     * @param DataItem $additionalData
     * @param Config $config
     * @param Configuration $configuration
     * @param Crosssell $crosssell
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Helper\Product\Configuration $configuration,
        \Magento\Checkout\Block\Cart\Crosssell $crosssell,
        ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->checkoutSession = $checkoutSession;
        $this->crosssell = $crosssell;
        $this->productRepository = $productRepository;
    }

    /**
     * Get collection
     *
     * @return array|string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException|\Zend_Db_Statement_Exception
     */
    public function getCartCollection()
    {
        $cartdata = [];
        $productCollection = $this->checkoutSession->getQuote()->getItems();
        if ($productCollection) {
            $cartItems = $this->checkoutSession->getQuote()->getItems();
        $itemsData = [];


        foreach ($cartItems as $item) {
            $product = $this->productRepository->getById($item->getProductId());
            
            $itemsData[] = [
                'item_id' => $product->getSku(),
                'item_name' => $product->getName(),
                'original_price' => (float) $product->getPrice(),
                'final_price' => (float) $item->getPrice(),
                'discount' => (float) ($product->getPrice() - $item->getPrice()),
                'quantity' => (int) $item->getQty(),
                'currency' => $this->checkoutSession->getQuote()->getQuoteCurrencyCode(),
                'item_url' => $product->getProductUrl()
            ];
        }
        $cartdata = [
            "event" => "view_cart",
            "ecommerce" => [
                "currency" => $this->checkoutSession->getQuote()->getQuoteCurrencyCode(),
                "value" => $this->getCartTotal(),
                "tax" => $this->getTaxAmount(),
                "coupon" => $this->getCouponCode(),
                "items" => $itemsData
            ]
        ];


        return $cartdata;
        }
        return '';
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


    public function getCartTotal()
    {
        $quote = $this->checkoutSession->getQuote();
        return $quote->getGrandTotal();
    }


    public function getTaxAmount()
    {
        $totals = $this->checkoutSession->getQuote()->getTotals();
        return isset($totals['subtotal']) ? $totals['subtotal']->getBaseTaxAmount() : 0;
    }
    
}
