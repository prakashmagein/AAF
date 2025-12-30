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

namespace Aheadworks\RewardPoints\Block\Customer\RewardPointsBalance;

use Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface;
use Aheadworks\RewardPoints\Model\Config;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\View\Element\Html\Link\Current as LinkCurrent;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\DefaultPathInterface;

class Link extends LinkCurrent
{
    /**
     * @param Context $context
     * @param DefaultPathInterface $defaultPath
     * @param CustomerRewardPointsManagementInterface $customerRewardPointsService
     * @param CurrentCustomer $currentCustomer
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        private readonly Context $context,
        DefaultPathInterface $defaultPath,
        private readonly CustomerRewardPointsManagementInterface $customerRewardPointsService,
        private readonly CurrentCustomer $currentCustomer,
        private readonly Config $config,
        array $data = []
    ) {
        parent::__construct($this->context, $defaultPath, $data);
    }

    /**
     *  Get label name reward points
     */
    public function getLabel()
    {
        $websiteId = (int)$this->context->getStoreManager()->getWebsite()->getId();

        return __($this->config->getTabLabelNameRewardPoints($websiteId));
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $customerRewardPointsSpendRateByGroup = $this->customerRewardPointsService
            ->isCustomerRewardPointsSpendRateByGroup($this->currentCustomer->getCustomerId());
        $customerRewardPointsEarnRateByGroup = $this->customerRewardPointsService
            ->isCustomerRewardPointsEarnRateByGroup($this->currentCustomer->getCustomerId());

        if ($customerRewardPointsSpendRateByGroup || $customerRewardPointsEarnRateByGroup) {
            return parent::_toHtml();
        }
        return '';
    }
}
