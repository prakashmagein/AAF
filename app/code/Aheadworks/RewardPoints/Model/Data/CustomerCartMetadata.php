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

namespace Aheadworks\RewardPoints\Model\Data;

use Aheadworks\RewardPoints\Api\Data\CustomerCartMetadataInterface;
use Magento\Framework\DataObject;

class CustomerCartMetadata extends DataObject implements CustomerCartMetadataInterface
{
    /**
     * @inheritDoc
     */
    public function getRewardPointsBalanceQty()
    {
        return $this->getData(self::REWARD_POINTS_BALANCE_QTY);
    }

    /**
     * @inheritDoc
     */
    public function setRewardPointsBalanceQty($rewardPointsBalanceQty)
    {
        return $this->setData(self::REWARD_POINTS_BALANCE_QTY, $rewardPointsBalanceQty);
    }

    /**
     * @inheritDoc
     */
    public function getCanApplyRewardPoints()
    {
        return $this->getData(self::CAN_APPLY_REWARD_POINTS);
    }

    /**
     * @inheritDoc
     */
    public function setCanApplyRewardPoints($canApplyRewardPoints)
    {
        return $this->setData(self::CAN_APPLY_REWARD_POINTS, $canApplyRewardPoints);
    }

    /**
     * @inheritDoc
     */
    public function getRewardPointsMaxAllowedQtyToApply()
    {
        return $this->getData(self::REWARD_POINTS_MAX_ALLOWED_QTY_TO_APPLY);
    }

    /**
     * @inheritDoc
     */
    public function setRewardPointsMaxAllowedQtyToApply($rewardPointsMaxAllowedQtyToApply)
    {
        return $this->setData(self::REWARD_POINTS_MAX_ALLOWED_QTY_TO_APPLY, $rewardPointsMaxAllowedQtyToApply);
    }

    /**
     * @inheritDoc
     */
    public function getRewardPointsConversionRatePointToCurrencyValue()
    {
        return $this->getData(self::REWARD_POINTS_CONVERSION_RATE_POINT_TO_CURRENCY_VALUE);
    }

    /**
     * @inheritDoc
     */
    public function setRewardPointsConversionRatePointToCurrencyValue($rewardPointsConversionRatePointToCurrencyValue)
    {
        return $this->setData(
            self::REWARD_POINTS_CONVERSION_RATE_POINT_TO_CURRENCY_VALUE,
            $rewardPointsConversionRatePointToCurrencyValue
        );
    }

    /**
     * @inheritDoc
     */
    public function getAreRewardPointsApplied()
    {
        return $this->getData(self::ARE_REWARD_POINTS_APPLIED);
    }

    /**
     * @inheritDoc
     */
    public function setAreRewardPointsApplied($areRewardPointsApplied)
    {
        return $this->setData(self::ARE_REWARD_POINTS_APPLIED, $areRewardPointsApplied);
    }

    /**
     * @inheritDoc
     */
    public function getAppliedRewardPointsQty()
    {
        return $this->getData(self::APPLIED_REWARD_POINTS_QTY);
    }

    /**
     * @inheritDoc
     */
    public function setAppliedRewardPointsQty($appliedRewardPointsQty)
    {
        return $this->setData(self::APPLIED_REWARD_POINTS_QTY, $appliedRewardPointsQty);
    }

    /**
     * @inheritDoc
     */
    public function getAppliedRewardPointsAmount()
    {
        return $this->getData(self::APPLIED_REWARD_POINTS_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setAppliedRewardPointsAmount($appliedRewardPointsAmount)
    {
        return $this->setData(self::APPLIED_REWARD_POINTS_AMOUNT, $appliedRewardPointsAmount);
    }

    /**
     * Retrieve label name reward points for the specific customer cart
     *
     * @return string
     */
    public function getRewardPointsLabelName(): string
    {
        return $this->getData(self::REWARD_POINTS_LABEL_NAME);
    }

    /**
     * Set label name reward points for the specific customer cart
     *
     * @param string $rewardPointsLabelName
     * @return $this
     */
    public function setRewardPointsLabelName(string $rewardPointsLabelName): self
    {
        return $this->setData(self::REWARD_POINTS_LABEL_NAME, $rewardPointsLabelName);
    }

    /**
     * Retrieve tab label name reward points for the specific customer cart
     *
     * @return string
     */
    public function getRewardPointsTabLabelName(): string
    {
        return $this->getData(self::REWARD_POINTS_TAB_LABEL_NAME);
    }

    /**
     * Set tab label name reward points for the specific customer cart
     *
     * @param string $rewardPointsTabLabelName
     * @return $this
     */
    public function setRewardPointsTabLabelName(string $rewardPointsTabLabelName): self
    {
        return $this->setData(self::REWARD_POINTS_TAB_LABEL_NAME, $rewardPointsTabLabelName);
    }
}
