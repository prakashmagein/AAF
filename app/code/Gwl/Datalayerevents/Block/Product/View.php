<?php
declare(strict_types=1);

namespace Gwl\Datalayerevents\Block\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\ProductList\Related;
use Magento\Catalog\Block\Product\ProductList\Upsell;
use Magento\Catalog\Model\CategoryRepository;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class View extends \Magento\Framework\View\Element\Template
{
    
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var null
     */
    protected $initProduct;

    /**
     * @var UrlFinderInterface
     */
    protected $urlFinder;

    /**
     * @var \Magento\Catalog\Model\CategoryRepository
     */
    protected $categoryRepository;


    /**
     * @var Related
     */
    protected $related;

    /**
     * @var Upsell
     */
    protected $upsell;

    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;


    protected $categoryCollectionFactory;

    protected $serializer;

    /**
     * @param Context $context
     * @param RequestInterface $request
     * @param ProductRepositoryInterface $productRepository
     * @param UrlFinderInterface $urlFinder
     * @param CategoryRepository $categoryRepository
     * @param Related $related
     * @param Upsell $upsell
     * @param StockRegistryInterface $stockRegistry
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Template\Context $context,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Catalog\Block\Product\ProductList\Related $related,
        \Magento\Catalog\Block\Product\ProductList\Upsell $upsell,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        CollectionFactory $categoryCollectionFactory,
        SerializerInterface $serializer,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->request = $request;
        $this->productRepository = $productRepository;
        $this->urlFinder = $urlFinder;
        $this->categoryRepository = $categoryRepository;
        $this->related = $related;
        $this->upsell = $upsell;
        $this->stockRegistry = $stockRegistry;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->serializer = $serializer;
    }

    /**
     * Get value
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getProductCollection()
    {
        $viewItemData = [];

        $id = $this->request->getParam('product_id') ?? $this->request->getParam('id');
        $storeId = $this->_storeManager->getStore()->getId();
        $product = $this->productRepository->getById($id, false, (string)$storeId);
        if($product){
        $name = $product->getName();

        $store = $this->_storeManager->getStore($storeId);
        $currencyCode = $store->getCurrentCurrencyCode();

        $categoryIds = $product->getCategoryIds();
        $categoryCollection = $this->categoryCollectionFactory->create()
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('entity_id', ['in' => $categoryIds]);

        $categoryNames = [];

        foreach ($categoryCollection as $category) {
            $categoryNames[] = $category->getName();
        }


        $imageUrl = $this->getProductImageUrl($product);


        $stockItem = $this->stockRegistry->getStockItem($product->getId());
        $minQty = $stockItem->getMinSaleQty();
        
        $totalPrice = $product->getFinalPrice();
        $productId = $product->getId();
        $productName = $product->getName();
        $productSku = $product->getSku();
        $productPrice = $product->getFinalPrice();
        $originalPrice = $product->getPrice();
        $discount = $originalPrice - $productPrice;
        $quantity = $minQty;
        $category = $categoryNames;

        if ($product->getTypeId() == "bundle") {
            $productPrice = round($product->getPriceInfo()->getPrice('final_price')->getMinimalPrice()->getValue(),2);
            $totalPrice = round($product->getPriceInfo()->getPrice('final_price')->getMinimalPrice()->getValue(),2);
        }
        
        $viewItemData = [
            'event' => 'view_item',
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


        return $viewItemData;

    }

        return '';
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


    /**
     * Escaper.
     *
     * @return \Magento\Framework\Escaper
     */
    public function escaper()
    {
        return $this->_escaper;
    }


    /**
     * Get previous Url
     *
     * @return array|string
     * @throws NoSuchEntityException
     */
    public function getPreviousUrl()
    {
        $baseUrl = $this->_urlBuilder->getBaseUrl();
        $refererUrl = $this->getRequest()->getServer('HTTP_REFERER');
        if ($refererUrl) {
            $filterData = [
                \Magento\UrlRewrite\Service\V1\Data\UrlRewrite::REQUEST_PATH => str_replace($baseUrl, '', $refererUrl)
            ];
            $rewrite = $this->urlFinder->findOneByData($filterData);
            if ($rewrite) {
                $entityId = $rewrite->getEntityId();
                if ($rewrite->getEntityType() != "product") {
                    $category = $this->categoryRepository
                        ->get($entityId, $this->_storeManager->getStore()->getId());
                    return ['item_list_id' => $entityId, 'item_list_name' => $category->getName()];
                } else {
                    $requestUri = $this->getRequest()->getServer('REQUEST_URI');
                    if (strpos($refererUrl, $requestUri) !== false) {
                        return '';
                    }
                    $product = $this->productRepository->getById($entityId);
                    return ['item_list_id' => $entityId, 'item_list_name' => $product->getName()];
                }
            }
            return ['item_list_id' => '', 'item_list_name' => "Home Page"];
        }
        return '';
    }

    /**
     * Get item
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws \Zend_Db_Statement_Exception
     */
    public function getItem()
    {
        $id = $this->request->getParam('product_id') ?? $this->request->getParam('id');
        return $this->itemArray($id, 0, true);
    }


    /**
     * Render Item array
     *
     * @param int $id
     * @param int $index
     * @param bool $flag
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException|\Zend_Db_Statement_Exception
     */
    public function itemArray($id, $index = 0, $flag = false)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $product = $this->productRepository->getById($id, false, (string)$storeId);
        
        $store = $this->_storeManager->getStore($storeId);
        $currencyCode = $store->getCurrentCurrencyCode();

        $categoryIds = $product->getCategoryIds();
        $categoryCollection = $this->categoryCollectionFactory->create()
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('entity_id', ['in' => $categoryIds]);

        $categoryNames = [];
        foreach ($categoryCollection as $category) {
            $categoryNames[] = $category->getName();
        }
        $imageUrl = $this->getProductImageUrl($product);
        $stockItem = $this->stockRegistry->getStockItem($product->getId());
        $minQty = $stockItem->getMinSaleQty();
        
        $totalPrice = $product->getFinalPrice();
        $productId = $product->getId();
        $productName = $product->getName();
        $productSku = $product->getSku();
        $productPrice = $product->getFinalPrice();
        $originalPrice = (float)($product->getPrice());
        $discount = $originalPrice - $productPrice;
        $quantity = $minQty;
        $category = $categoryNames;
        $productUrl = $product->getProductUrl();

        if ($product->getTypeId() == "bundle") {
            $productPrice = round($product->getPriceInfo()->getPrice('final_price')->getMinimalPrice()->getValue(),2);
        }

        $item['item_name'] = $productName;
        $item['item_id'] = $productId;
        $item['original_price'] = $originalPrice;
        $item['price'] = $productPrice;
        $item['discount'] = $discount;
        $item['currency'] = $currencyCode;
        $item['item_brand'] = '';
        $item['item_category'] = $category;
        $item['item_list_name'] = '';
        $item['item_list_id'] = '';
        $item['quantity'] = $quantity;
        $item['item_url'] = $productUrl;
        $item['index'] = 1;
        return $item;
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


    public function getItemPrice()
    {
        $id = $this->request->getParam('product_id') ?? $this->request->getParam('id');
        $storeId = $this->_storeManager->getStore()->getId();
        $product = $this->productRepository->getById($id, false, (string)$storeId);
        
        $totalPrice = $product->getFinalPrice();
        
        if ($product->getTypeId() == "bundle") {
            $totalPrice = round($product->getPriceInfo()->getPrice('final_price')->getMinimalPrice()->getValue(),2);
        }

        return $totalPrice;
    }

    public function getCurrencyCode()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $store = $this->_storeManager->getStore($storeId);
        return $store->getCurrentCurrencyCode();
    }


    public function getProductImage()
    {
        $id = $this->request->getParam('product_id') ?? $this->request->getParam('id');
        $storeId = $this->_storeManager->getStore()->getId();
        $product = $this->productRepository->getById($id, false, (string)$storeId);
        return $this->getProductImageUrl($product);
    }


    
}
