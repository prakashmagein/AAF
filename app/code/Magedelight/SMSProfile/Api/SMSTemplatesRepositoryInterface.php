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
use Magedelight\SMSProfile\Api\Data\SMSTemplatesInterface;
use Magento\Framework\Exception\NoSuchEntityException;

interface SMSTemplatesRepositoryInterface
{
    /**
     * Function save
     *
     * @param  SMSTemplatesInterface $smsTemplate
     * @return SMSTemplatesInterface
     */
    public function save(SMSTemplatesInterface $smsTemplate);

    /**
     * Function delete
     *
     * @param  SMSTemplatesInterface $smsTemplate
     * @return void
     */
    public function delete(SMSTemplatesInterface $smsTemplate);

    /**
     * Function get By id
     *
     * @param  int $id
     * @return SMSTemplatesInterface
     * @throws NoSuchEntityException
     */
    public function getById($id);

    /**
     * Function get event by type
     *
     * @param  string $eventType
     * @param  string $storeId
     * @return SMSTemplatesInterface
     * @throws NoSuchEntityException
     */
    public function getByEventType($eventType, $storeId);
}
