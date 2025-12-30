<?php
/**
 * Magedelight
 * Copyright (C) 2022 Magedelight <info@magedelight.com>
 *
 * @category  Magedelight
 * @package   Magedelight_SMSProfile
 * @author    Magedelight <info@magedelight.com>
 * @copyright 2022 Mage Delight (http://www.magedelight.com/)
 * @license   http://opensource.org/licenses/gpl-3.0.html (GPL-3.0)
 * @link      https://www.magedelight.com/
 */

namespace Magedelight\SMSProfile\Api;

/**
* Declare all function defination
*
* @api
*/

use Magento\Framework\Api\SearchCriteriaInterface;
use Magedelight\SMSProfile\Api\Data\SMSProfileOtpAttemptInterface;
use Magento\Framework\Exception\NoSuchEntityException;

interface SMSProfileOtpAttemptRepositoryInterface
{
    /**
     * Function save
     *
     * @param  SMSProfileOtpAttemptInterface $smsProfileOtpAttempt
     * @return SMSTemplatesInterface
     */
    public function save(SMSProfileOtpAttemptInterface $smsProfileOtpAttempt);

    /**
     * Function delete
     *
     * @param  SMSProfileOtpAttemptInterface $smsProfileOtpAttempt
     * @return void
     */
    public function delete(SMSProfileOtpAttemptInterface $smsTemplate);

    /**
     * Function get By id
     *
     * @param  int $id
     * @return SMSProfileOtpAttemptInterface
     * @throws NoSuchEntityException
     */
    public function getById($id);
}
