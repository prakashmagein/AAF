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

namespace Magedelight\SMSProfile\Model\Config\Source;

use Magedelight\SMSProfile\Model\ResourceModel\SMSMailTemplates\CollectionFactory as SMSMailTemplatesCollFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

class MailTemplateOption implements \Magento\Framework\Option\ArrayInterface
{

     /**
      * @var \Magento\Framework\Registry
      */
    private $_coreRegistry;

    /**
     * @var \Magento\Email\Model\Template\Config
     */
    private $_emailConfig;

    /**
     * @var \Magento\Email\Model\ResourceModel\Template\CollectionFactory
     */
    protected $_templatesFactory;

    /**
     * @var SMSMailTemplatesCollFactory
     */
    private $smsMailTemplatesCollFactory;

    /**
     * DataPersistorInterface
     *
     * @var dataPersistor
     */
    private $dataPersistor;

    /**
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Email\Model\ResourceModel\Template\CollectionFactory $templatesFactory
     * @param \Magento\Email\Model\Template\Config $emailConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Email\Model\ResourceModel\Template\CollectionFactory $templatesFactory,
        \Magento\Email\Model\Template\Config $emailConfig,
        SMSMailTemplatesCollFactory $smsMailTemplatesCollFactory,
        DataPersistorInterface $dataPersistor,
        array $data = []
    ) {
       // parent::__construct($data);
        $this->_coreRegistry = $coreRegistry;
        $this->_templatesFactory = $templatesFactory;
        $this->_emailConfig = $emailConfig;
        $this->smsMailTemplatesCollFactory = $smsMailTemplatesCollFactory;
        $this->dataPersistor = $dataPersistor;
    }
     /**
      * Generate list of email templates
      *
      * @return array
      */
    public function toOptionArray()
    {
        /** @var $collection \Magento\Email\Model\ResourceModel\Template\Collection */
        
            $collection = $this->_templatesFactory->create();
            $smsMailTemplatesColl = $this->smsMailTemplatesCollFactory->create();
            $smstemplate = $this->dataPersistor->get('smstemplates');
            $currTemplateId = 0;
        if ($smstemplate!='') {
            $currTemplateId  = $smstemplate->getTemplateCode();
        }
        if ($currTemplateId) {
            $collection->addFieldToFilter('template_id', $currTemplateId);
        } else {
            if ($smsMailTemplatesColl->getSize() > 0) {
                $emailIdArray = [];
                foreach ($smsMailTemplatesColl as $smsMailTemplatesModel) {
                    $emailIdArray[] = $smsMailTemplatesModel->getTemplateCode();
                }
                $collection->addFieldToFilter('template_id', ['nin'=>$emailIdArray]);
            }
        }

            $collection->load();
            $options = $collection->toOptionArray();
            array_unshift($options, ['value' => '', 'label' => __('Select Email Template')]);
            return $options;
    }
}
