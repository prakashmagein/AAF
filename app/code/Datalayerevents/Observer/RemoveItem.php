<?php
declare(strict_types=1);

namespace Gwl\Datalayerevents\Observer;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

/**
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class RemoveItem implements ObserverInterface
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var RequestInterface
     */
    protected $request;

    protected $logger;

    protected $productRepository;

    protected $categoryCollectionFactory;

   
    /**
     * @param Session $session
     * @param RequestInterface $request
     */
    public function __construct(
        Session $session,
        \Magento\Framework\App\RequestInterface $request,
        LoggerInterface $logger,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        CollectionFactory $categoryCollectionFactory
    ) {
        $this->session = $session;
        $this->request = $request;
        $this->logger = $logger;
        $this->productRepository = $productRepository;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * Set data to event
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $item = $observer->getQuoteItem();

        $cartEventData = [];

        $quote = $item->getQuote();
        $currencyCode = $quote->getQuoteCurrencyCode();

        $product = $this->productRepository->getById($item->getProduct()->getId());
        $imageUrl = $this->getProductImageUrl($product);



        $categoryIds = $product->getCategoryIds();
        $categoryCollection = $this->categoryCollectionFactory->create()
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('entity_id', ['in' => $categoryIds]);

        $categoryNames = [];

        foreach ($categoryCollection as $category) {
            $categoryNames[] = $category->getName();
        }

        
        $totalPrice = $item->getRowTotal();
        $productId = $item->getProduct()->getId();
        $productName = $item->getProduct()->getName();
        $productSku = $item->getProduct()->getSku();
        $productPrice = $item->getProduct()->getFinalPrice();
        $originalPrice = $item->getProduct()->getPrice();
        $discount = $originalPrice - $productPrice;
        $productImage = $item->getProduct()->getImage();
        $quantity = $item->getQty() ?? 1;
        $category = $categoryNames;

        $cartEventData = [
            'event' => 'remove_from_cart',
            'ecommerce' => [
                'currency' => $currencyCode,
                'value' => $totalPrice,
                'item_img' => $imageUrl,
                'items' => [
                    [
                        "item_name" => $productName,
                        "item_id" => $productSku,
                        "original_price" => $originalPrice,
                        "price" => $productPrice,
                        "discount" => $discount,
                        "currency" => $currencyCode,
                        "item_brand" => "",
                        "item_category" => $category,
                        "item_list_name" => "",
                        "item_list_id" => "",
                        "quantity" => $quantity
                    ]
                ]
            ]
        ];


        $this->logger->info('Remove Added to Cart array: ' . json_encode($cartEventData));


        if ($this->session->getRemoveItem()) {
            $this->session->unsRemoveItem();
        }
        $this->session->setRemoveItem($cartEventData);
    }



        protected function getProductImageUrl($product)
        {
            $image = $product->getImage();

            if ($image && $image != 'no_selection') {
                $mediaUrl = $this->getMediaUrl();
                return $mediaUrl . 'catalog/product' . $image;
            }

            return null;
        }

        protected function getMediaUrl()
        {
            return \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Store\Model\StoreManagerInterface::class)
                ->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        }
}
