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
namespace Aheadworks\RewardPoints\Model;


use Aheadworks\RewardPoints\Model\ResourceModel\OrderExtra as OrderAdditionalResource;

/**
 * Class Aheadworks\RewardPoints\Model\OrderExtra
 */
class OrderExtra extends \Magento\Framework\Model\AbstractModel implements OrderExtraInterface
{
    /**
     * {@inheritDoc}
     */
    protected function _construct()
    {
        $this->_init(OrderAdditionalResource::class);
    }

    /**
     * {@inheritDoc}
     */
    public function setOrderId(int $orderId): self
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrderId(): int
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function setAppliedRuleIds($ruleIds): self
    {
        return $this->setData(self::APPLIED_RULE_IDS, $ruleIds);
    }

    /**
     * {@inheritDoc}
     */
    public function getAppliedRuleIds(): ?array
    {
        return $this->getData(self::APPLIED_RULE_IDS);
    }

    /**
     * {@inheritDoc}
     */
    public function setCanceledRuleIds($ruleIds): self
    {
        return $this->setData(self::CANCELED_RULE_IDS, $ruleIds);
    }

    /**
     * {@inheritDoc}
     */
    public function getCanceledRuleIds(): ?array
    {
        return $this->getData(self::CANCELED_RULE_IDS);
    }
}
