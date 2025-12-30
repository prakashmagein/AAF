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

namespace Aheadworks\RewardPoints\Model;

/**
 * Class Flag
 *
 * @package Aheadworks\RewardPoints\Model
 */
class Flag extends \Magento\Framework\Flag
{
    /**#@+
     * Constants for reward points flags
     */
    const AW_RP_EXPIRATION_CHECK_LAST_EXEC_TIME = 'aw_rp_expiration_check_last_exec_time';
    const AW_RP_EXPIRATION_REMINDER_LAST_EXEC_TIME = 'aw_rp_expiration_reminder_last_exec_time';
    const AW_RP_CUSTOMER_BIRTHDAY_LAST_EXEC_TIME = 'aw_rp_customer_birthday_last_exec_time';
    const AW_RP_HOLDING_PERIOD_EXPIRATION_CHECK_LAST_EXEC_TIME = 'aw_rp_holding_period_expiration_check_last_exec_time';
    /**#@-*/

    /**
     * Setter for flag code
     *
     * @param string $code
     * @return $this
     */
    public function setRewardPointsFlagCode(string $code): self
    {
        $this->_flagCode = $code;
        return $this;
    }
}
