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

use Magedelight\SMSProfile\Api\SMSTemplatesRepositoryInterface;
use Magedelight\SMSProfile\Api\Data\SMSTemplatesInterface;
use Magedelight\SMSProfile\Model\ResourceModel\SMSTemplates\CollectionFactory;
use Magedelight\SMSProfile\Model\ResourceModel\SMSTemplates\Collection;
use Magedelight\SMSProfile\Model\SMSTemplatesFactory;
use Magedelight\SMSProfile\Model\SMSTemplates;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;

class SMSTemplatesRepository implements SMSTemplatesRepositoryInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var SMSTemplatesFactory
     */
    private $smsTemplateFactory;

    /**
     * @var \Magedelight\SMSProfile\Model\ResourceModel\SMSTemplates
     */
    private $resourceModel;

    /**
     * SMSTemplatesRepository constructor.
     * @param SMSTemplatesFactory $smsTemplate
     * @param CollectionFactory $collectionFactory
     * @param \Magedelight\SMSProfile\Model\ResourceModel\SMSTemplates $resourceModel
     */
    
    public function __construct(
        \Magedelight\SMSProfile\Model\ResourceModel\SMSTemplates $resourceModel,
        SMSTemplatesFactory $smsTemplate,
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

    public function save(SMSTemplatesInterface $smsTemplate)
    {
        try {
            $this->resourceModel->save($smsTemplate);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $smsTemplate;
    }

    public function delete(SMSTemplatesInterface $smsTemplate)
    {
        try {
            $this->resourceModel->delete($smsTemplate);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    public function getByEventType($eventType, $storeId)
    {
        $smsTemplate = $this->collectionFactory->create();
        $smsTemplate->addFieldToFilter('store_id', $storeId);
        $smsTemplate->addFieldToFilter('event_type', $eventType);
        $smsTemplate->load();
        
        if ($smsTemplate->getSize() == 0) {
            $smsTemplate = $this->collectionFactory->create();
            $smsTemplate->addFieldToFilter('store_id', 0);
            $smsTemplate->addFieldToFilter('event_type', $eventType);
            $smsTemplate->load();

            if ($smsTemplate->getSize() == 0) {
                throw new NoSuchEntityException(__('smsTemplate with '.$eventType.' does not exist.'));
            }
        }

        foreach ($smsTemplate as $smsTemplate) {
            if (!$smsTemplate->getId()) {
                throw new NoSuchEntityException(
                    __('smsTemplate with event type "%1" does not exist.', $smsTemplate->getId())
                );
            }
        }
        return $smsTemplate;
    }
}
