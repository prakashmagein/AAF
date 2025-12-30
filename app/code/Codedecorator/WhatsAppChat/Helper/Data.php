<?php

namespace Codedecorator\WhatsAppChat\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Url\DecoderInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Serialize\Serializer\Json;
/**
 * Class Data
 * @package Codedecorator\DisableRightClick\Helper
 */
class Data extends AbstractHelper
{
    /**
     *
     */
    const API_URL = 'aHR0cHM6Ly93d3cuY29kZWRlY29yYXRvci5jb20vY2Rtb2R1bGUvcmVnaXN0ZXIvbGl2ZQ==';
    /**
     *
     */
    const XML_WHATSAPPCHAT_ENABLE = 'whatsappchat/general/enable';
    const XML_WHATSAPPCHAT_NUMBER = 'whatsappchat/general/number';
    const XML_WHATSAPPCHAT_MESSAGE = 'whatsappchat/general/message';
    const XML_WHATSAPPCHAT_IMAGE = 'whatsappchat/general/image';
    const XML_WHATSAPPCHAT_PRODUCT = 'whatsappchat/general/product';
    const XML_WHATSAPPCHAT_POSITION = 'whatsappchat/general/position';


    /**
     * @var DecoderInterface
     */
    protected $urlDecoder;
    /**
     * @var Curl
     */
    protected $curlClient;
    protected $json;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Data constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param DecoderInterface $urlDecoder
     * @param Curl $curl
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Json $json,
        DecoderInterface $urlDecoder,
        Curl $curl
    )
    {
        $this->json = $json;
        $this->urlDecoder = $urlDecoder;
        $this->storeManager = $storeManager;
        $this->curlClient = $curl;
        parent::__construct($context);
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isEnable($storeId = NULL)
    {
        if ($this->scopeConfig->getValue(self::XML_WHATSAPPCHAT_ENABLE, ScopeInterface::SCOPE_STORE, $storeId)) {
            return true;
        }
        return false;
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function getWhatsAppData($storeId = NULL)
    {
        if ($this->isEnable($storeId)) {

            try {
                $configData['number'] = $this->scopeConfig->getValue(self::XML_WHATSAPPCHAT_NUMBER, ScopeInterface::SCOPE_STORE, $storeId);
                $configData['message'] = $this->scopeConfig->getValue(self::XML_WHATSAPPCHAT_MESSAGE, ScopeInterface::SCOPE_STORE, $storeId);
                $configData['image'] = $this->scopeConfig->getValue(self::XML_WHATSAPPCHAT_IMAGE, ScopeInterface::SCOPE_STORE, $storeId);
                $configData['product'] = $this->scopeConfig->getValue(self::XML_WHATSAPPCHAT_PRODUCT, ScopeInterface::SCOPE_STORE, $storeId);
                $configData['position'] = $this->scopeConfig->getValue(self::XML_WHATSAPPCHAT_POSITION, ScopeInterface::SCOPE_STORE, $storeId);

                return $configData;
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }


    /**
     * @return null
     */
    public function installModule()
    {

        try {
            $installDomain = $this->storeManager->getStore()->getBaseUrl();
            if (strpos($installDomain, 'localhost') == false && strpos($installDomain, '127.0.0.1') == false) {
                $installData = [
                    'module' => $this->_getModuleName(),
                    'domain' => $installDomain
                ];
                $this->getCurlClient()->post($this->urlDecoder->decode(self::API_URL), $installData);
                $this->getCurlClient()->getBody();
            }
            return null;
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }


    /**
     * @return Curl
     */
    public function getCurlClient()
    {
        return $this->curlClient;
    }

}