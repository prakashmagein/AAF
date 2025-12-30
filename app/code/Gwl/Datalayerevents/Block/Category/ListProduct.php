<?php
declare(strict_types=1);

namespace Gwl\Datalayerevents\Block\Category;

use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

class ListProduct extends \Magento\Framework\View\Element\Template implements \Magento\Framework\View\Element\Block\ArgumentInterface
{

    /**
     * @var Layer
     */
    protected $catalogLayer;

    /**
     * @var Resolver
     */
    protected $layerResolver;

    /**
     * @var array
     */
    protected $data;

    protected $serializer;

    protected $storeManager;

    protected $categoryCollectionFactory;

    protected $catalogSession;

    /**
     * @param Context $context
     * @param Resolver $layerResolver
     * @param DataItem $additionalData
     * @param Config $config
     * @param Data $dataHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Resolver $layerResolver,
        SerializerInterface $serializer,
        StoreManagerInterface $storeManager,
        CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Model\Session $catalogSession,
        array $data = []
    ) {
        $this->layerResolver = $layerResolver;
        $this->catalogLayer = $layerResolver->get();
        $this->data = $data;
        $this->serializer = $serializer;
        $this->storeManager = $storeManager;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->catalogSession = $catalogSession;
        parent::__construct($context, $data);
    }

    /**
     * Get current category
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getCurrentCategory()
    {
        return $this->catalogLayer->getCurrentCategory();
    }

    /**
     * Get view list
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException|\Zend_Db_Statement_Exception
     */
    public function getViewItemList()
    {
        $productCollection = $this->catalogLayer->getProductCollection();
        if ($productCollection->count()) {
            $items = [];
            $index = 1;
            foreach ($productCollection as $product) {
                $categoryIds = $product->getCategoryIds();
                $categoryNames = $this->getCategory($categoryIds);
                
                if ($product->getTypeId() == "simple") {
                    $item["price"] = $product->getFinalPrice(1);
                }
                $item['original_price'] = (float)($product->getPrice());
                $item['discount'] = (float) ($product->getPrice() - $product->getFinalPrice(1));
                $item["item_list_id"] = $this->getCurrentCategory()->getId();
                $item["item_list_name"] = $this->getItemListName();
                $item['item_name'] = $product->getName();
                $item['item_id'] = $product->getSku();
                $item['currency'] = $this->getCurrencyCode();
                $item['item_brand'] = '';
                $item['index'] = $index;
                $item['item_category'] = $categoryNames;
                $items[] = $item;
                $index++;
            }
            return array_chunk($items, 50);
        }
        return [];
    }

    /**
     * Get item list name
     *
     * @return string
     * @throws LocalizedException
     */
    public function getItemListName()
    {
        if ($this->getRequest()->getFullActionName() == "catalogsearch_result_index") {
            return $this->getLayout()->getBlock('page.main.title')
                ->getPageTitle()->getText();
        }
        return $this->catalogLayer->getCurrentCategory()->getName();
    }
    /**
     * Is enable module
     *
     * @return mixed
     */
    public function isEnableModule()
    {
        return $this->config->enableModule();
    }

    /**
     * Serialize item
     *
     * @param array $data
     * @return bool|string
     */
    public function serializeItem($data)
    {
        return $this->serializer->serialize($data);
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

    public function getCategory($categoryIds){
        $categoryCollection = $this->categoryCollectionFactory->create()
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('entity_id', ['in' => $categoryIds]);

        $categoryNames = [];

        foreach ($categoryCollection as $category) {
            $categoryNames[] = $category->getName();
        }

        return $categoryNames;
    }

    public function getCurrencyCode()
    {
        $storeId = $this->storeManager->getStore()->getId();
        $store = $this->storeManager->getStore($storeId);
        return $store->getCurrentCurrencyCode();
    }

    /**
     * Get CatalogSession
     *
     * @return \Magento\Catalog\Model\Session
     */
    public function getCatalogSession()
    {
        return $this->catalogSession;
    }
}
