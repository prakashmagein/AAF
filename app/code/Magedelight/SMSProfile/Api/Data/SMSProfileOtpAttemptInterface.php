<?php
/**
 * Magedelight
 * Copyright (C) 2022 Magedelight <info@magedelight.com>
 *
 * @category  Magedelight
 * @package   Magedelight_SMSProfile
 * @copyright Copyright (c) 2022 Mage Delight (http://www.magedelight.com/)
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author    Magedelight <info@magedelight.com>
 */

namespace Magedelight\SMSProfile\Api\Data;

/**
 * @api
 */

interface SMSProfileOtpAttemptInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return void
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getCustomerMobile();

    /**
     * @param string $templateName
     *
     * @return void
     */
    public function setCustomerMobile($templateName);


    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @param string $templateContent
     *
     * @return void
     */
    public function setCustomerId($templateContent);

    /**
     * @return string
     */
    public function getAttempCount();

    /**
     * @param string $eventType
     *
     * @return void
     */
    public function setAttempCount($eventType);
    /**
     * @return  datetime
     */
    public function getResendCountTime();

    /**
     * @param   datetime $resendCountTime
     *
     * @return void
     */
    public function setResendCountTime($storeId);
}
