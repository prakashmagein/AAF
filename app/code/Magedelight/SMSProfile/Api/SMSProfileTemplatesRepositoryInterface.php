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

namespace Magedelight\SMSProfile\Api;

/**
* @api
*/

use Magento\Framework\Api\SearchCriteriaInterface;
use Magedelight\SMSProfile\Api\Data\SMSProfileTemplatesInterface;
use Magento\Framework\Exception\NoSuchEntityException;

interface SMSProfileTemplatesRepositoryInterface
{
    /**
     * function
     *
     * @param  SMSProfileTemplatesInterface $smsTemplate
     * @return SMSProfileTemplatesInterface
     */

    public function save(SMSProfileTemplatesInterface $smsTemplate);

    /**
     * function
     *
     * @param SMSProfileTemplatesInterface $smsTemplate
     * @return void
     */
    public function delete(SMSProfileTemplatesInterface $smsTemplate);

    /**
     * function
     *
     * @param int $id
     * @return SMSProfileTemplatesInterface
     * @throws NoSuchEntityException
     */

    public function getById($id);

    /**
     * function
     *
     * @param string $eventType
     * @param string $storeId
     * @return SMSProfileTemplatesInterface
     * @throws NoSuchEntityException
     */

    public function getByEventType($eventType, $storeId);
}
