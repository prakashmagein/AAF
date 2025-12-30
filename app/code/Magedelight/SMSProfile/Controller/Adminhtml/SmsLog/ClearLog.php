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
 
namespace Magedelight\SMSProfile\Controller\Adminhtml\SmsLog;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magedelight\SMSProfile\Model\SMSLogFactory;

class ClearLog extends Action
{
    /**
     * @var string
     */
    const ACTION_RESOURCE = 'Magedelight_SMSProfile::smslog';

    /**
     * RedirectFactory
     *
     * @var resultRedirect
     */
    private $resultRedirect;

    /**
     * @var SMSLogFactory
     */
    private $smslog;

    /**
     * @param Context $context
     * @param SMSLogFactory $smslog
     * @param RedirectFactory $resultRedirect
     */
    public function __construct(
        Context $context,
        SMSLogFactory $smslog,
        RedirectFactory $resultRedirect
    ) {
        parent::__construct($context);
        $this->resultRedirect = $resultRedirect;
        $this->smslog = $smslog;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ACTION_RESOURCE);
    }

    /**
     * SmsLog clear for AJAX request
     *
     * @return RedirectFactory
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirect->create();
        $sms  = $this->smslog->create();
        try {
            $sms->clearelog();
            $this->messageManager->addSuccess(__('The Notificaton Sms Log has been cleared.'));
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        return $resultRedirect->setPath('*/*/');
    }
}
