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
 
namespace Magedelight\SMSProfile\Model;

use Magedelight\SMSProfile\Api\Data\SMSMailTemplatesInterface;
use Magento\Framework\Model\AbstractModel;

class SMSMailTemplates extends AbstractModel implements SMSMailTemplatesInterface
{
    
    const CACHE_TAG = 'smsMailTemplates';

    protected $_cacheTag = 'smsMailTemplates';
    
    protected $_eventPrefix = 'smsMailTemplates';
    
    protected function _construct()
    {
        $this->_init('Magedelight\SMSProfile\Model\ResourceModel\SMSMailTemplates');
    }
    
    public function getDefaultValues()
    {
        $values = [];
        return $values;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData('entity_id', $id);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->getData('entity_id');
    }

    /**
     * @param string $mailTemplateName
     * @return $this
     */
    public function setMailTemplateName($mailTemplateName)
    {
        return $this->setData('mail_template_name', $mailTemplateName);
    }

    /**
     * @return string
     */
    public function getMailTemplateName()
    {
        return $this->getData('mail_template_name');
    }

    /**
     * @param string $mailTemplateSubject
     * @return $this
     */
    public function setMailTemplateSubject($mailTemplateSubject)
    {
        return $this->setData('mail_template_subject', $mailTemplateSubject);
    }

    /**
     * @return string
     */
    public function getMailTemplateSubject()
    {
        return $this->getData('mail_template_subject');
    }

    /**
     * @param string $mailTemplateContent
     * @return $this
     */
    public function setMailTemplateContent($mailTemplateContent)
    {
        return $this->setData('mail_template_content', $mailTemplateContent);
    }

    /**
     * @return string
     */
    public function getMailTemplateContent()
    {
        return $this->getData('mail_template_content');
    }

    /**
     * @param string $mailTemplateStyle
     * @return $this
     */
    public function setMailTemplateStyle($mailTemplateStyle)
    {
        return $this->setData('mail_template_style', $mailTemplateStyle);
    }

    /**
     * @return string
     */
    public function getMailTemplateStyle()
    {
        return $this->getData('mail_template_style');
    }


    /**
     * @param int $smsForFrontendUser
     * @return $this
     */
    public function setSmsForFrontendUser($smsForFrontendUser)
    {
        return $this->setData('sms_for_frontend_user', $smsForFrontendUser);
    }

    /**
     * @return int
     */
    public function getSmsForFrontendUser()
    {
        return $this->getData('sms_for_frontend_user');
    }

    /**
     * @param string $smsTemplateLanguage
     * @return $this
     */
    public function setSmsTemplateLanguage($smsTemplateLanguage)
    {
        return $this->setData('sms_template_language', $smsTemplateLanguage);
    }

    /**
     * @return string
     */
    public function getSmsTemplateLanguage()
    {
        return $this->getData('sms_template_language');
    }

    /**
     * @param string $smsTemplateContent
     * @return $this
     */
    public function setSmsTemplateContent($smsTemplateContent)
    {
        return $this->setData('sms_template_content', $smsTemplateContent);
    }

    /**
     * @return string
     */
    public function getSmsTemplateContent()
    {
        return $this->getData('sms_template_content');
    }
}
