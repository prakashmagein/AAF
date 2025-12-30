<?php
namespace Gwl\Datalayerevents\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Image;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;


class CartProductAddAfter implements ObserverInterface
{
    protected $session;

    protected $logger;

    protected $productRepository;
    protected $imageHelper;
    protected $storeManager;
    protected $categoryCollectionFactory;


    public function __construct(
        \Magento\Checkout\Model\Session $session,
        LoggerInterface $logger,
        ProductRepositoryInterface $productRepository,
        Image $imageHelper,
        StoreManagerInterface $storeManager,
        CollectionFactory $categoryCollectionFactory
    ) {
        $this->session = $session;
        $this->logger = $logger;
        $this->productRepository = $productRepository;
        $this->imageHelper = $imageHelper;
        $this->storeManager = $storeManager;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    public function execute(Observer $observer)
    {
        $eventdata = [];


        $product = $this->productRepository->getById($observer->getProduct()->getId());

        

        $categoryIds = $observer->getProduct()->getCategoryIds();



        $categoryCollection = $this->categoryCollectionFactory->create()
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('entity_id', ['in' => $categoryIds]);

        $categoryNames = [];

        foreach ($categoryCollection as $category) {
            $categoryNames[] = $category->getName();
        }

        $this->logger->info('Category Names: ' . json_encode($categoryNames));

        $this->logger->info('Category Added to Cart array: ' . json_encode($categoryIds));



        $currency_code =  $this->storeManager->getStore()->getCurrentCurrencyCode();

        $imageUrl = $this->storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ) . 'catalog/product' . $product->getImage();

        $imageUrl = $this->imageHelper->init($product, 'product_base_image')->getUrl();

        

        if ($observer->getProduct()->getTypeId() != "grouped") {

            $originalPrice = $product->getPriceInfo()->getPrice('regular_price')->getValue();

            $data = [
                'isAddToCart' => true,
                'productId' => $observer->getProduct()->getId(),
                'productName' => $observer->getProduct()->getName(),
                'qty' => $observer->getProduct()->getQty(),
                'image' => $imageUrl,
                'currency' => $currency_code,
                'category' => $categoryNames,
                'originalPrice' => $originalPrice
            ];

            
            $price = (float)$observer->getQuoteItem()->getProduct()
                    ->getFinalPrice($observer->getProduct()->getQty()) * $observer->getProduct()->getQty();
            if ($observer->getProduct()->getTypeId() == "bundle") {
                $price = $observer->getQuoteItem()->getPrice();
            }
            $data["price"] = $price;
            if ($observer->getProduct()->getTypeId() == "configurable") {
                $item = $observer->getQuoteItem();
                $data["productId"] = array_keys($item->getQtyOptions())[0];
                $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
                $variant = [];
                foreach ($options['attributes_info'] as $option) {
                    $variant[] = $option['label'] . ": " . $option['value'];
                }
                if ($variant) {
                    $data['variant'] = implode(',', $variant);
                }
            }

            // var_dump($data);
            // die();


                 $discountAmount = $data['originalPrice'] - $data['price'];

                  $eventdata =   [
                            'event' => 'add_to_cart',
                            'ecommerce' => [
                                'currency' => $data['currency'],
                                'value' => $price,
                                'item_img' => $data['image'],
                                'items' => [
                                    [
                                        'item_name' => $data['productName'],
                                        'item_id' => $data['productId'],
                                        'original_price' => $data['originalPrice'],
                                        'price' => $data['price'],
                                        'discount' => $discountAmount,
                                        'currency' => $data['currency'],
                                        'item_brand' => 'brand A',
                                        'item_category' => $data['category'],
                                        'item_list_name' => 'Loungewear',
                                        'item_list_id' => 'CATID',
                                        'quantity' => $data['qty'],
                                    ],
                                ],
                            ],
                        ];

            
            $this->logger->info('Product Added to Cart array: ' . json_encode($eventdata));

            if ($this->session->getDataLayerScriptAddtocart()) {
                $this->session->unsDataLayerScriptAddtocart();
            }
            $this->session->setDataLayerScriptAddtocart($eventdata);



        }
    }
}