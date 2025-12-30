<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

declare(strict_types=1);

namespace Magefan\OrderEdit\Block\Adminhtml\Order\Shipping;

use Magento\Backend\Block\Template;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Backend\Model\Session\Quote as SessionQuote;
use Magento\Framework\App\ProductMetadataInterface;

class Price extends Template
{
    /**
     * @var SessionQuote
     */
    protected $sessionQuote;

    protected $shippingRateGroups;

    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @param Template\Context $context
     * @param SessionQuote $sessionQuote
     * @param array $data
     * @param JsonHelper|null $jsonHelper
     * @param DirectoryHelper|null $directoryHelper
     * @param ProductMetadataInterface|null $productMetadata
     */
    public function __construct(
        Template\Context $context,
        SessionQuote $sessionQuote,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null,
        ProductMetadataInterface $productMetadata = null
    ) {
        $this->sessionQuote = $sessionQuote;
        $this->productMetadata = $productMetadata ?:  \Magento\Framework\App\ObjectManager::getInstance()
            ->get(ProductMetadataInterface::class);

        if (version_compare($this->productMetadata->getVersion(), '2.4.0', '<')) {
            parent::__construct($context, $data);
        } else {
            parent::__construct($context, $data, $jsonHelper, $directoryHelper);
        }
    }

    /**
     * @return string
     */
    public function getCustomPrice(): string
    {
        if (empty($this->shippingRateGroups)) {
            $this->shippingRateGroups = $this->getAddress()->getGroupedAllShippingRates();
        }

        foreach ($this->shippingRateGroups as $rateGroup) {
            foreach ($rateGroup as $rate) {
                if ($this->isMethodActive($rate->getCode())) {
                    $customPrice = (string)$this->getQuote()->getData('mf_custom_shipping_price');

                    if ('' !== $customPrice) {
                        return $customPrice;
                    }

                    return '';
                }
            }
        }

        return '';
    }

    /**
     * @param $code
     * @return bool
     */
    public function isMethodActive($code): bool
    {
        return $code === $this->getShippingMethod();
    }

    /**
     * @return string
     */
    public function getShippingMethod(): string
    {
        return $this->getAddress()->getShippingMethod();
    }

    /**
     * @return \Magento\Quote\Model\Quote\Address
     */
    public function getAddress()
    {
        return $this->getQuote()->getShippingAddress();
    }

    /**
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->_getSession()->getQuote();
    }

    /**
     * @return SessionQuote
     */
    protected function _getSession()
    {
        return $this->sessionQuote;
    }
}
