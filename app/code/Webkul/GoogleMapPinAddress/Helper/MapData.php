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
namespace Webkul\GoogleMapPinAddress\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Locale\Resolver;

class MapData extends \Magento\Framework\App\Helper\AbstractHelper implements ArgumentInterface
{
    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;
       
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $directoryHelper;

    /**
     * @var \Magento\Customer\Helper\Address
     */
    protected $customerHelperAddress;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $_encryptor;

    /**
     * @var Resolver
     */
    protected $localeResolver;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;

    /**
     * Construct function
     *
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magento\Customer\Helper\Address $customerHelperAddress
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param Resolver $localeResolver
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Customer\Helper\Address $customerHelperAddress,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\Serialize\Serializer\Json $json,
        Resolver $localeResolver
    ) {
        $this->localeResolver = $localeResolver;
        $this->scopeConfig = $scopeConfig;
        $this->directoryHelper = $directoryHelper;
        $this->customerHelperAddress = $customerHelperAddress;
        $this->_encryptor = $encryptor;
        $this->json = $json;
        parent::__construct($context);
    }

    /**
     * Get Api Key
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->_encryptor->decrypt($this->scopeConfig
                    ->getValue(
                        'googlemappinaddress/gmpa_settings/api_key',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    ));
    }

    /**
     * Get Module Status
     *
     * @return integer
     */
    public function getModuleStatus()
    {
        return $this->scopeConfig
                ->getValue(
                    'googlemappinaddress/gmpa_settings/active',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
    }

    /**
     * Get Directory Data
     *
     * @return \Magento\Directory\Helper\Data
     */
    public function getDirectoryData()
    {
        return $this->directoryHelper;
    }

    /**
     * Get Customer Helper Address
     *
     * @return \Magento\Customer\Helper\Address
     */
    public function getCustomerHelAdd()
    {
        return $this->customerHelperAddress;
    }

    /**
     * Get Default Latitude
     *
     * @return float
     */
    public function getDefualtLatitude()
    {
        return $this->scopeConfig
                ->getValue(
                    'googlemappinaddress/gmpa_settings/default_latitude',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
    }

    /**
     * Get Default Longitude
     *
     * @return float
     */
    public function getDefualtLongitude()
    {
        return $this->scopeConfig
                    ->getValue(
                        'googlemappinaddress/gmpa_settings/default_longitude',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    );
    }

    /**
     * Get Locale Language
     *
     * @return string
     */
    public function getCurrentLocale()
    {
        return $this->localeResolver->getLocale(); // fr_CA
    }

    /**
     * This function will return json encoded data
     *
     * @param  array $data
     * @return string
     */
    public function jsonEncodeData($data)
    {
        return $this->json->serialize($data);
    }

    /**
     * This function will return json decode data
     *
     * @param  string $data
     * @return array
     */
    public function jsonDecodeData($data)
    {
        return $this->json->unserialize($data);
    }
}
