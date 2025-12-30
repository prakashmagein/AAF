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

use Magedelight\SMSProfile\Api\Data\SMSTemplatesInterface;
use Magento\Framework\Model\AbstractModel;

class SMSTemplates extends AbstractModel implements SMSTemplatesInterface
{
    
    const CACHE_TAG = 'smsTemplates';

    protected $_cacheTag = 'smsTemplates';
    
    protected $_eventPrefix = 'smsTemplates';
    
    protected function _construct()
    {
        $this->_init('Magedelight\SMSProfile\Model\ResourceModel\SMSTemplates');
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
     * @param string $templateName
     * @return $this
     */
    public function setTemplateName($templateName)
    {
        return $this->setData('template_name', $templateName);
    }

    /**
     * @return string
     */
    public function getTemplateName()
    {
        return $this->getData('template_name');
    }

    /**
     * @param string $templateContent
     * @return $this
     */
    public function setTemplateContent($templateContent)
    {
        return $this->setData('template_content ', $templateContent);
    }

    /**
     * @return string
     */
    public function getOtpTemplate()
    {
        return $this->getData('otp_template');
    }

    /**
     * @param string $otpTemplate
     *
     * @return void
     */
    public function setOtpTemplate($otpTemplate)
    {
        return $this->setData('otp_template ', $otpTemplate);
    }


    /**
     * @return string
     */
    public function getNotificationTemplate()
    {
        return $this->getData('notification_template');
    }

    /**
     * @param string $notificationTemplate
     *
     * @return void
     */
    public function setNotificationTemplate($notificationTemplate)
    {
        return $this->setData('notification_template ', $notificationTemplate);
    }

    /**
     * @return string
     */
    public function getTemplateContent()
    {
        return $this->getData('template_content ');
    }

    /**
     * @param string $eventType
     * @return $this
     */
    public function setEventType($eventType)
    {
        return $this->setData('event_type ', $eventType);
    }

    /**
     * @return string
     */
    public function getEventType()
    {
        return $this->getData('event_type');
    }

    /**
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->setData('store_id', $storeId);
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->getData('store_id');
    }
}
