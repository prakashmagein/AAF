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

namespace Magedelight\SMSProfile\Model;

use Magedelight\SMSProfile\Api\SMSProfileTemplatesRepositoryInterface;
use Magedelight\SMSProfile\Api\Data\SMSProfileTemplatesInterface;
use Magedelight\SMSProfile\Model\ResourceModel\SMSProfileTemplates\CollectionFactory;
use Magedelight\SMSProfile\Model\ResourceModel\SMSProfileTemplates\Collection;
use Magedelight\SMSProfile\Model\SMSProfileTemplatesFactory;
use Magedelight\SMSProfile\Model\SMSProfileTemplates;
use Magedelight\SMSProfile\Model\ResourceModel\SMSProfileTemplates as ResourceModelSmsProfileTemaplate;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;

class SMSProfileTemplatesRepository implements SMSProfileTemplatesRepositoryInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var SMSProfileTemplatesFactory
     */
    private $smsProfileTemplateFactory;

    /**
     * @var \Magedelight\SMSProfile\Model\ResourceModel\smsProfileTemplate
     */
    private $resourceModel;

    /**
     * SMSTemplatesRepository constructor.
     * @param SMSProfileTemplatesFactory $smsProfileTemplate
     * @param CollectionFactory $collectionFactory
     * @param ResourceModelSmsProfileTemaplate $resourceModel
     */
    
    public function __construct(
        ResourceModelSmsProfileTemaplate $resourceModel,
        SMSProfileTemplatesFactory $smsProfileTemplate,
        CollectionFactory $collectionFactory
    ) {
        $this->resourceModel = $resourceModel;
        $this->smsProfileTemplateFactory = $smsProfileTemplate;
        $this->collectionFactory = $collectionFactory;
    }

    public function getById($id)
    {
        $smsProfileTemplate = $this->smsProfileTemplateFactory->create();
        $this->resourceModel->load($smsProfileTemplate, $id);
        if (!$smsProfileTemplate->getId()) {
            throw new NoSuchEntityException(__('smsProfileTemplate with id "%1" does not exist.', $id));
        }
        return $smsProfileTemplate;
    }

    public function save(SMSProfileTemplatesInterface $smsProfileTemplate)
    {
        try {
            $this->resourceModel->save($smsProfileTemplate);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $smsProfileTemplate;
    }

    public function delete(SMSProfileTemplatesInterface $smsProfileTemplate)
    {
        try {
            $this->resourceModel->delete($smsProfileTemplate);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    public function getByEventType($eventType, $storeId)
    {
        $smsProfileTemplate = $this->collectionFactory->create();
        $smsProfileTemplate->addFieldToFilter('store_id', $storeId);
        $smsProfileTemplate->addFieldToFilter('event_type', $eventType);
        $smsProfileTemplate->load();
        if ($smsProfileTemplate->getSize() == 0) {
            $smsProfileTemplate = $this->collectionFactory->create();
            $smsProfileTemplate->addFieldToFilter('store_id', 0);
            $smsProfileTemplate->addFieldToFilter('event_type', $eventType);
            $smsProfileTemplate->load();
            if ($smsProfileTemplate->getSize() == 0) {
                return __('SmsProfileTemplate with '.$eventType.' does not exist.')->getText();
            }
        }
        foreach ($smsProfileTemplate as $smsProfileTemplate) {
            if (!$smsProfileTemplate->getId()) {
                return __('smsProfileTemplate with event type "%1" does not exist.', $id)->getText();
            }
        }
        return $smsProfileTemplate;
    }
}
