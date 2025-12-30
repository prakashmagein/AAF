<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://aheadworks.com/end-user-license-agreement/
 *
 * @package    RewardPoints
 * @version    2.4.0
 * @copyright  Copyright (c) 2024 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
declare(strict_types=1);

namespace Aheadworks\RewardPoints\Block\Adminhtml\Sales\Order\Create;

use Aheadworks\RewardPoints\Model\Calculator\Validator as CartPriceValidator;
use Aheadworks\RewardPoints\Model\Config;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface;
use Magento\Sales\Model\AdminOrder\Create;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Backend\Model\Session\Quote;

class Payment extends Template
{
    /**
     * @param Context $context
     * @param Create $orderCreate
     * @param PriceCurrencyInterface $priceCurrency
     * @param CustomerRewardPointsManagementInterface $customerRewardPointsService
     * @param Quote $sessionQuote
     * @param Config $config
     * @param CartPriceValidator $cartPriceValidator
     * @param array $data
     */
    public function __construct(
        private readonly Context $context,
        private readonly Create $orderCreate,
        private readonly PriceCurrencyInterface $priceCurrency,
        private readonly CustomerRewardPointsManagementInterface $customerRewardPointsService,
        private readonly Quote $sessionQuote,
        private readonly Config $config,
        private readonly CartPriceValidator $cartPriceValidator,
        array $data = []
    ) {
        parent::__construct($this->context, $data);
    }

    /**
     * Retrieve quote
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->orderCreate->getQuote();
    }

    /**
     * Show reward points or not
     *
     * @return bool
     * @throws \Zend_Db_Select_Exception
     */
    public function canShow()
    {
        return $this->getBalance() > 0;
    }

    /**
     * Retrieve customer balance
     *
     * @return float
     * @throws \Zend_Db_Select_Exception
     */
    public function getBalance()
    {
        if (!$this->getQuote() || !$this->getQuote()->getCustomerId()){
            return 0.0;
        }
        $websiteId = (int)$this->getQuote()->getStore()->getWebsiteId();
        if ($this->config->areRestrictSpendingPointsWithCartPriceRules($websiteId)
            && $this->cartPriceValidator->canApplySalesRules($this->getQuote())) {
            return 0.0;
        }

        return $this->customerRewardPointsService
            ->getCustomerRewardPointsBalanceBaseCurrency($this->getQuote()->getCustomerId());
    }

    /**
     * Format value as price
     *
     * @param float $value
     * @return string
     */
    public function formatPrice($value)
    {
        return $this->priceCurrency->convertAndFormat(
            $value,
            true,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $this->sessionQuote->getStore()
        );
    }

    /**
     * Get label name reward points
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getLabelNameRewardPoints(): string
    {
        $websiteId = (int)$this->context->getStoreManager()->getWebsite()->getId();

        return $this->config->getLabelNameRewardPoints($websiteId);
    }

    /**
     * Check whether quote uses customer balance
     *
     * @return bool
     */
    public function isUseAwRewardPoints()
    {
        return $this->getQuote()->getAwUseRewardPoints();
    }
}
