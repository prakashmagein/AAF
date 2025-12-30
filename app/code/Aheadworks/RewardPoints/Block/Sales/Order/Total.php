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

namespace Aheadworks\RewardPoints\Block\Sales\Order;

use Aheadworks\RewardPoints\Model\Config;
use Magento\Framework\DataObject\Factory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Total extends Template
{
    /**
     * @param Context $context
     * @param Factory $factory
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        private readonly Context $context,
        private readonly Factory $factory,
        private readonly Config $config,
        array $data = []
    ) {
        parent::__construct($this->context, $data);
    }

    /**
     * Retrieve sales order model
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        $parentBlock = $this->getParentBlock();
        if ($parentBlock) {
            return $parentBlock->getOrder();
        }
        return null;
    }

    /**
     * Retrieve totals source object
     *
     * @return \Magento\Sales\Model\Order|\Magento\Sales\Model\Order\Invoice
     */
    public function getSource()
    {
        $parentBlock = $this->getParentBlock();
        if ($parentBlock) {
            return $parentBlock->getSource();
        }
        return null;
    }

    /**
     * Initialize reward points order total
     *
     * @return Total
     * @throws LocalizedException
     */
    public function initTotals()
    {
        $order = $this->getOrder();
        if ($order) {
            $source = $this->getSource();
            if ($source) {
                $websiteId = (int)$this->context->getStoreManager()->getWebsite()->getId();
                if ($order->getAwUseRewardPoints()) {
                    $this->getParentBlock()->addTotal(
                        $this->factory->create(
                            [
                                'code'   => 'aw_reward_points',
                                'strong' => false,
                                'label'  => $source->getAwRewardPointsDescription(),
                                'value'  => $source->getAwRewardPointsAmount(),
                            ]
                        )
                    );
                }
                if ($source->getAwRewardPointsBlnceRefund()) {
                    $this->getParentBlock()->addTotal(
                        $this->factory->create(
                            [
                                'code'       => 'aw_reward_points_refund',
                                'strong'     => false,
                                'label'      =>
                                    __('%1 Returned to %2',
                                        $source->getAwRewardPointsBlnceRefund(),
                                        $this->config->getLabelNameRewardPoints($websiteId)
                                    ),
                                'value'      => $source->getAwRewardPointsRefund()
                            ]
                        ),
                        'last'
                    );
                }
                if ($source->getAwRewardPointsBlnceReimbursed()) {
                    $this->getParentBlock()->addTotal(
                        $this->factory->create(
                            [
                                'code'       => 'aw_reward_points_reimbursed',
                                'strong'     => false,
                                'label'      => __(
                                    '%1 Reimbursed spent %2',
                                    $source->getAwRewardPointsBlnceReimbursed(),
                                    $this->config->getLabelNameRewardPoints($websiteId)
                                ),
                                'value'      => $source->getAwRewardPointsReimbursed()
                            ]
                        ),
                        'last'
                    );
                }
            }
        }
        return $this;
    }
}
