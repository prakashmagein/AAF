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
namespace Aheadworks\RewardPoints\Model\Validator\Config;

use Aheadworks\RewardPoints\Api\Data\EarnRateInterface;

/**
 * Class Rates
 *
 * @package Aheadworks\RewardPoints\Model\Validator\Config
 */
class Rates
{
    /**
     * Checks whether a config has a duplicate rates
     *
     * @param array $rates
     * @return bool
     */
    public function hasDuplicateValue($rates)
    {
        if (!is_array($rates)) {
            return false;
        }

        $clearRates = array_filter($rates, 'is_array');
        foreach ($clearRates as $key => $rate) {
            foreach ($clearRates as $comparedKey => $comparedRate) {
                if ($key === $comparedKey) {
                    continue;
                }
                if ($this->isDuplicateValue($rate, $comparedRate)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Checks whether rates are a duplicate
     *
     * @param array $rate
     * @param array $comparedRate
     * @return bool
     */
    private function isDuplicateValue($rate, $comparedRate)
    {
        if ($rate[EarnRateInterface::WEBSITE_ID] != $comparedRate[EarnRateInterface::WEBSITE_ID]) {
            return false;
        }

        if ($rate[EarnRateInterface::CUSTOMER_GROUP_ID] != $comparedRate[EarnRateInterface::CUSTOMER_GROUP_ID]) {
            return false;
        }

        if ($rate[EarnRateInterface::LIFETIME_SALES_AMOUNT]
            != $comparedRate[EarnRateInterface::LIFETIME_SALES_AMOUNT]
        ) {
            return false;
        }

        return true;
    }
}
