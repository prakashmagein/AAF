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

use Magedelight\SMSProfile\Api\SMSMailTemplatesRepositoryInterface;
use Magedelight\SMSProfile\Api\Data\SMSMailTemplatesInterface;
use Magedelight\SMSProfile\Model\ResourceModel\SMSMailTemplates\CollectionFactory;
use Magedelight\SMSProfile\Model\ResourceModel\SMSMailTemplates\Collection;
use Magedelight\SMSProfile\Model\SMSMailTemplatesFactory;
use Magedelight\SMSProfile\Model\SMSMailTemplates;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;

class SMSMailTemplatesRepository implements SMSMailTemplatesRepositoryInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var SMSMailTemplatesFactory
     */
    private $smsTemplateFactory;

    /**
     * @var \Magedelight\SMSProfile\Model\ResourceModel\SMSMailTemplates
     */
    private $resourceModel;

    /**
     * SMSMailTemplatesRepository constructor.
     * @param SMSMailTemplatesFactory $smsTemplate
     * @param CollectionFactory $collectionFactory
     * @param \Magedelight\SMSProfile\Model\ResourceModel\SMSMailTemplates $resourceModel
     */
    
    public function __construct(
        \Magedelight\SMSProfile\Model\ResourceModel\SMSMailTemplates $resourceModel,
        SMSMailTemplatesFactory $smsTemplate,
        CollectionFactory $collectionFactory
    ) {
        $this->resourceModel = $resourceModel;
        $this->smsTemplateFactory = $smsTemplate;
        $this->collectionFactory = $collectionFactory;
    }

    public function getById($id)
    {
        $smsTemplate = $this->smsTemplateFactory->create();
        $this->resourceModel->load($smsTemplate, $id);
        if (!$smsTemplate->getId()) {
            throw new NoSuchEntityException(__('smsTemplate with id "%1" does not exist.', $id));
        }
        return $smsTemplate;
    }

    public function save(SMSMailTemplatesInterface $smsTemplate)
    {
        
        try {
            $this->resourceModel->save($smsTemplate);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }

        return $smsTemplate;
    }

    public function delete(SMSMailTemplatesInterface $smsTemplate)
    {
        try {
            $this->resourceModel->delete($smsTemplate);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        }

        return true;
    }

    public function getTemplateBySubject($subject)
    {
        $smsTemplate = $this->collectionFactory->create();
        $smsTemplate->addFieldToFilter('mail_template_subject', $subject);
        $smsTemplate->load();
        foreach ($smsTemplate as $smsTemplate) {
            if (!$smsTemplate->getId()) {
                throw new NoSuchEntityException(
                    __('sms mail Template with subject "%1" does not exist.', $smsTemplate->getId())
                );
            }
        }
        return $smsTemplate;
    }

    public function getByCode($code)
    {
        $smsTemplate = $this->collectionFactory->create();
        $smsTemplate->addFieldToFilter('template_code', $code);
        $smsTemplate->load();
        foreach ($smsTemplate as $smsTemplate) {
            if (!$smsTemplate->getId()) {
                throw new NoSuchEntityException(
                    __('sms mail Template with code "%1" does not exist.', $smsTemplate->getId())
                );
            }
        }
        return $smsTemplate;
    }
}
