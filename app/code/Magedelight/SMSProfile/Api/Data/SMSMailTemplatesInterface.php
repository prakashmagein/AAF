<?php
/**
 * Magedelight
 * Copyright (C) 2022 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_SMSProfile
 * @copyright Copyright (c) 2022 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */
 
namespace Magedelight\SMSProfile\Api\Data;

/**
 * @api
 */

interface SMSMailTemplatesInterface
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
    public function getMailTemplateName();

    /**
     * @param string $mailTemplateName
     *
     * @return void
     */
    public function setMailTemplateName($mailTemplateName);


    /**
     * @return string
     */
    public function getMailTemplateSubject();

    /**
     * @param string $mailTemplateSubject
     *
     * @return void
     */
    public function setMailTemplateSubject($mailTemplateSubject);

    /**
     * @return string
     */
    public function getMailTemplateStyle();

    /**
     * @param string $mailTemplateStyle
     *
     * @return void
     */
    public function setMailTemplateStyle($mailTemplateStyle);
    
    /**
     * @return int
     */
    public function getSmsForFrontendUser();

    /**
     * @param int $smsForFrontendUser
     *
     * @return void
     */
    public function setSmsForFrontendUser($smsForFrontendUser);

    /**
     * @return string
     */
    public function getSmsTemplateLanguage();

    /**
     * @param string $smsTemplateContent
     *
     * @return void
     */
    public function setSmsTemplateLanguage($smsTemplateContent);
    

    /**
     * @return string
     */
    public function getSmsTemplateContent();

    /**
     * @param string $smsTemplateContent
     *
     * @return void
     */
    public function setSmsTemplateContent($smsTemplateContent);
}
