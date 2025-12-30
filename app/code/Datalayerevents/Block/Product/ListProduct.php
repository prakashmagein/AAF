<?php
declare(strict_types=1);
namespace Gwl\Datalayerevents\Block\Product;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

class ListProduct implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;


    /**
     * @var \Bss\GA4\Helper\Data
     */
    protected $dataHelper;

    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * Current category. (Set current category)
     *
     * @var \Magento\Catalog\Model\Category
     */
    protected $currentCategory;

    /**
     * Full action name request. (Set full action name)
     *
     * @var string
     */
    protected $fullActionName = '';

    /**
     * Layout. (Set layout)
     *
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * @var \Magento\Catalog\Model\Session
     */
    protected $catalogSession;

    protected $storeManager;

    protected $categoryCollectionFactory;

    /**
     * Construct.
     *
     * @param DataItem $additionalData
     * @param SerializerInterface $serializer
     * @param Config $config
     * @param \Bss\GA4\Helper\Data $dataHelper
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Catalog\Model\Session $catalogSession
     */
    public function __construct(
        SerializerInterface $serializer,
        \Magento\Framework\Escaper $escaper,
        \Magento\Catalog\Model\Session $catalogSession,
        StoreManagerInterface $storeManager,
        CollectionFactory $categoryCollectionFactory
    ) {
        $this->serializer = $serializer;
        $this->escaper = $escaper;
        $this->catalogSession = $catalogSession;
        $this->storeManager = $storeManager;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * Get current category.
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getCurrentCategory($category = null)
    {
        if ($category) {
            $this->currentCategory = $category;
        }

        return $this->currentCategory;
    }

    /**
     * Get view list
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException|\Zend_Db_Statement_Exception
     */
    public function getViewItemList($productCollection)
    {
        if ($productCollection->count()) {
            $items = [];
            $index = 1;
            
            foreach ($productCollection as $product) {

                $categoryIds = $product->getCategoryIds();

                $categoryNames = $this->getCategory($categoryIds);
                
                if ($product->getTypeId() == "simple") {
                    //default qty in list item set is 1
                    $item["price"] = $product->getFinalPrice(1);
                }
                $item['original_price'] = (float)($product->getPrice());
                $item['discount'] = (float) ($product->getPrice() - $product->getFinalPrice(1));
                $item["item_list_id"] = $this->getCurrentCategory()->getId();
                $item["item_list_name"] = $this->getItemListName($this->fullActionName, $this->layout);
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
    public function getItemListName($fullActionName, $layout)
    {
        /* Set action name and layout. */
        $this->fullActionName = $fullActionName;
        $this->layout = $layout;

        if ($fullActionName === "catalogsearch_result_index") {
            //return $layout->getBlock('page.main.title')->getPageTitle()->getText();
            return $layout->getBlock('catalogsearch.leftnav')->getRequest()->getParam('q');
        }

        return $this->getCurrentCategory()->getName();
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
        return $this->escaper;
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

    /**
     * Get gtag when ajax complete
     *
     * @param array $dataItemsList
     * @param int|string $categoryId
     * @param string $itemListName
     *
     * @return array
     */
    public function getGtagAjaxLayer($dataItemsList, $categoryId, $itemListName)
    {
        $gtag = [];
        if (isset($dataItemsList) && isset($categoryId) && isset($itemListName)) {
            foreach ($dataItemsList as $listItem) {
                $gtag[] = '[
                    "event", "view_item_list", {
                        "item_list_id" : "'. $categoryId .'",
                        "item_list_name" : "'. $itemListName .'",
                        "items": '. $this->serializeItem($listItem) .'
                    }
                ]';
            }
        }
        return $gtag;
    }


    public function getCurrencyCode()
    {
        $storeId = $this->storeManager->getStore()->getId();
        $store = $this->storeManager->getStore($storeId);
        return $store->getCurrentCurrencyCode();
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
}
