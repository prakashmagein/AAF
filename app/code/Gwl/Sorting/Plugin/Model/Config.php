<?php
namespace Gwl\Sorting\Plugin\Model;

use Magento\Store\Model\StoreManagerInterface;
class Config
{
    protected $_storeManager;

public function __construct(
    StoreManagerInterface $storeManager
) {
    $this->_storeManager = $storeManager;

}

/**
 * Adding custom options and changing labels
 *
 * @param \Magento\Catalog\Model\Config $catalogConfig
 * @param [] $options
 * @return []
 */
public function afterGetAttributeUsedForSortByArray(\Magento\Catalog\Model\Config $catalogConfig, $options)
{
    $store = $this->_storeManager->getStore();
        $currencySymbol = $store->getCurrentCurrency()->getCurrencySymbol();

        unset($options['price']);

        $options['name'] = __('A - Z');

        //New sorting options
       // $customOption['desc_name'] = __('Z - A');

        $customOption['price'] = __('Price Low To High');

        $options['high_to_low_price'] = __('Price High To Low');

        $options = array_merge($customOption, $options);
	asort($options);
    return $options;
}
}
