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
use Magedelight\SMSProfile\Api\Data\SMSMailTemplatesInterface;
use Magento\Framework\Exception\NoSuchEntityException;

interface SMSMailTemplatesRepositoryInterface
{
    /**
     * Function save
     *
     * @param  SMSMailTemplatesInterface $smsTemplate
     * @return SMSMailTemplatesInterface
     */
    public function save(SMSMailTemplatesInterface $smsTemplate);

    /**
     * Function delete
     *
     * @param  SMSMailTemplatesInterface $smsTemplate
     * @return void
     */
    public function delete(SMSMailTemplatesInterface $smsTemplate);

    /**
     * Function get By id
     *
     * @param  int $id
     * @return SMSMailTemplatesInterface
     * @throws NoSuchEntityException
     */
    public function getById($id);

    /**
     * Function get template by subject
     *
     * @param  string $subject
     * @return SMSMailTemplatesInterface
     * @throws NoSuchEntityException
     */
    public function getTemplateBySubject($subject);

    /**
     * Function get By code
     *
     * @param  int $code
     * @return SMSMailTemplatesInterface
     * @throws NoSuchEntityException
     */
    public function getByCode($code);
}
