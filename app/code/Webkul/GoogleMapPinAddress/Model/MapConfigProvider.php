<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_GoogleMapPinAddress
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\GoogleMapPinAddress\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class MapConfigProvider implements ConfigProviderInterface
{
   /**
    * @var \Webkul\GoogleMapPinAddress\Helper\MapData
    */
    protected $helperData;

    /**
     * Construct
     *
     * @param \Webkul\GoogleMapPinAddress\Helper\MapData $helperData
     */
    public function __construct(
        \Webkul\GoogleMapPinAddress\Helper\MapData $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * Set data in window.checkout.config for checkout page.
     *
     * @return array $options
     */
    public function getConfig()
    {
        $options = [
            'map' => []
        ];
        $options['map']['status'] = $this->helperData->getModuleStatus();
        $options['map']['api_key'] = $this->helperData->getApiKey();
        $options['map']['default_latitude'] = $this->helperData->getDefualtLatitude();
        $options['map']['default_longitude'] = $this->helperData->getDefualtLongitude();
        return $options;
    }
}
