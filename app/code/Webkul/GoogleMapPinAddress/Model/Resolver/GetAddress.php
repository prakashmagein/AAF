<?php
declare(strict_types=1);
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_GoogleMapPinAddress
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\GoogleMapPinAddress\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Book field resolver, used for GraphQL request processing
 */
class GetAddress implements ResolverInterface
{
    /**
     * @var \Webkul\GoogleMapPinAddress\Helper\MapData
     */
    protected $helper;

    /**
     * @var \magento\framework\Filesystem\Driver\File $file
     */
    protected $file;

    /**
     * @param \Webkul\GoogleMapPinAddress\Helper\MapData $helper
     * @param \magento\framework\Filesystem\Driver\File $file
     */
    public function __construct(
        \Webkul\GoogleMapPinAddress\Helper\MapData $helper,
        \magento\framework\Filesystem\Driver\File $file
    ) {
        $this->helper = $helper;
        $this->file = $file;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $lat = $args['input']['lat'];
        $lng = $args['input']['lng'];
        $key = $this->helper->getApiKey();
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$lat.",".$lng."&key=".$key;
        $data = $this->helper->jsonDecodeData($this->file->fileGetContents($url));
        $postcode = '';
        $city = '';
        $country = '';
        $state = '';
        foreach ($data['results']['1']['address_components'] as $address) {
            if ($address['types']['0']=='administrative_area_level_3') {
                $city = $address['long_name'];
            }
            if ($address['types']['0']=='postal_code') {
                $postcode = $address['long_name'];
            }
            if ($address['types']['0']=='country') {
                $country = $address['long_name'];
            }
            if ($address['types']['0']=='administrative_area_level_1') {
                $state = $address['long_name'];
            }
        }
        $street = "";
        foreach ($data['results']['0']['address_components'] as $address) {
            if ($address['types']['0']=='street_number') {
                $street = $street.$address['long_name'];
            }
            if ($address['types']['0']=='route') {
                $street = $street.','.$address['long_name'];
            }
            if ($address['types']['0']=='neighborhood') {
                $street = $street.','.$address['long_name'];
            }
            if ($address['types']['0']=='locality') {
                $street = $street.','.$address['long_name'];
            }
        }
        $add[] = [
            'street' => $street,
            'city' => $city,
            'state' => $state,
            'country' => $country,
            'postcode' => $postcode
        ];
        return $add;
    }
}
