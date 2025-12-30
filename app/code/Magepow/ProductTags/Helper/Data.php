<?php

namespace Magepow\ProductTags\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var array
     */
    protected $configModule;
    protected $_urlMedia;
    public $_storeManager;

    protected $_registry;
    protected $resourceModelTag;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,      
        \Magepow\ProductTags\Model\ResourceModel\Tag $resourceModelTag,
        \Magento\Framework\Registry $registry
    )
    {
        parent::__construct($context);
        $this->configModule = $this->getConfig(strtolower($this->_getModuleName()));
        $this->_storeManager = $storeManager;
        $this->resourceModelTag = $resourceModelTag;
        $this->_registry = $registry;
    }

    public function getConfig($cfg='')
    {
        if($cfg) return $this->scopeConfig->getValue( $cfg, \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
        return $this->scopeConfig;
    }

    public function getConfigModule($cfg='', $value=null)
    {
        $values = $this->configModule;
        if( !$cfg ) return $values;
        $config  = explode('/', $cfg);
        $end     = count($config) - 1;
        foreach ($config as $key => $vl) {
            if( isset($values[$vl]) ){
                if( $key == $end ) {
                    $value = $values[$vl];
                }else {
                    $values = $values[$vl];
                }
            } 

        }
        return $value;
    }

    public function getMediaUrl($file="")
    {   
        $route = $this->getConfigModule("general/route");
        $route = $route ?? 'producttags';
        $url = $route."/".$file;
        if(!$this->_urlMedia) $this->_urlMedia = $this->_storeManager->getStore()->getBaseUrl();
        return $this->_urlMedia . $url;
    }

    public function getCurrentProduct()
    {        
        $product = $this->_registry->registry('current_product');
        return $product->getId();
    }      
    
    public function getTagModel($tag)
    {     
        if($this->getConfigModule("general/enable_tag_all_product")){
            $productId = $this->getCurrentProduct();
            $data = $this->resourceModelTag->getProductsPosition($tag);
            if(array_key_exists($productId, $data)){
                return true;
            }
            return false;
        } 
        return true; 
        
    }  
}