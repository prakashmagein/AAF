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

interface SMSProfileTemplatesInterface
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
    public function getTemplateName();

    /**
     * @param string $templateName
     *
     * @return void
     */
    public function setTemplateName($templateName);

    /**
     * @return string
     */
    public function getOtpTemplate();

    /**
     * @param string $otpTemplate
     *
     * @return void
     */
    public function setOtpTemplate($otpTemplate);

    /**
     * @return string
     */
    public function getNotificationTemplate();

    /**
     * @return string
     */
    public function getTemplateContent();

    /**
     * @param string $templateContent
     *
     * @return void
     */
    public function setTemplateContent($templateContent);

    /**
     * @return string
     */
    public function getEventType();

    /**
     * @param string $eventType
     *
     * @return void
     */
    public function setEventType($eventType);
    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $storeId
     *
     * @return void
     */
    public function setStoreId($storeId);
}
