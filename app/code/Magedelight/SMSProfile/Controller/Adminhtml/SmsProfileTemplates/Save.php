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
 
namespace Magedelight\SMSProfile\Controller\Adminhtml\SmsProfileTemplates;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session as BackendSession;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magedelight\SMSProfile\Model\SMSProfileTemplatesFactory;
use Magedelight\SMSProfile\Api\SMSProfileTemplatesRepositoryInterface;

class Save extends Action
{
    /**
    * @var string
    */
    const ACTION_RESOURCE = 'Magedelight_SMSProfile::smsprofiletemplates';

    /**
     * sms profile templates  factory
     *
     * @var SMSProfileTemplatesFactory
     */
    private $smsProfileTemplates;

    /**
     * SMSProfileTemplatesRepositoryInterface
     *
     * @var SMSProfileTemplatesRepositoryInterface
     */
    private $smsProfileTemplatesRepository;

    /**
     * BackendSession
     *
     * @var backendSession
     */
    private $backendSession;

    /**
     * RedirectFactory
     *
     * @var resultRedirect
     */
    private $resultRedirect;

     /**
      * DataPersistorInterface
      *
      * @var dataPersistor
      */
    private $dataPersistor;

    private $collectionFactory;

     /**
      * @param RedirectFactory  $resultRedirect
      * @param SMSProfileTemplatesFactory $smsProfileTemplates
      * @param SMSProfileTemplatesRepositoryInterface $smsProfileTemplatesRepository
      * @param BackendSession $backendSession
      * @param DataPersistorInterface $dataPersistor
      * @param Context $context
      */

    public function __construct(
        Context $context,
        RedirectFactory $resultRedirect,
        BackendSession $backendSession,
        DataPersistorInterface $dataPersistor,
        SMSProfileTemplatesFactory $smsProfileTemplates,
        SMSProfileTemplatesRepositoryInterface $smsProfileTemplatesRepository,
        \Magedelight\SMSProfile\Model\ResourceModel\SMSProfileTemplates\CollectionFactory $collectionFactory
    ) {
        $this->resultRedirect = $resultRedirect;
        $this->backendSession = $backendSession;
        $this->smsProfileTemplates  = $smsProfileTemplates;
        $this->dataPersistor = $dataPersistor;
        $this->smsProfileTemplatesRepository  = $smsProfileTemplatesRepository;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ACTION_RESOURCE);
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $data['store_id'] =$data['store_id'][0];
        $resultRedirect = $this->resultRedirect->create();
        $smsProfileTemplate =  $this->smsProfileTemplates->create();

        if (!isset($data['event_type'])) {
            $notification_templates = $this->collectionFactory->create()->addFieldToFilter('event_type', $data['event_type'])->addFieldToFilter('store_id', $data['store_id']);
            if (count($notification_templates) < 1) {
                try {
                    $this->smsProfileTemplatesRepository->save($smsProfileTemplate->setData($data));
                    $this->messageManager->addSuccess(__('You saved this Sms Profile Template.'));
                    $this->dataPersistor->clear('smsprofiletemplate');
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Something went wrong while saving data.'));
                }
            } else {
                foreach ($notification_templates as $key => $notification_template) {
                    $this->messageManager->addSuccess(__("This Event is already saved. Please apply changes here."));
                    return $resultRedirect->setPath('*/*/edit', ['entity_id' => $notification_template->getEntityId()]);
                }
            }
        } else {
            try {
                $this->smsProfileTemplatesRepository->save($smsProfileTemplate->setData($data));
                $this->messageManager->addSuccess(__('You saved this Sms Profile Template.'));
                $this->dataPersistor->clear('smsprofiletemplate');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving data.'));
            }
            return $resultRedirect->setPath('*/*/');
        }
    }
}
