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
 
namespace Magedelight\SMSProfile\Controller\Adminhtml\SmsProfileLog;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magedelight\SMSProfile\Model\SMSProfileLogFactory;

class ClearLog extends Action
{
    /** @var string */
    const ACTION_RESOURCE = 'Magedelight_SMSProfile::smsprofilelog';

    /** @var RedirectFactory */
    private $resultRedirect;

    /** @var SMSProfileLogFactory */
    private $smsprofilelog;

    /**
     * @param Context $context
     * @param SMSProfileLogFactory $smsprofilelog
     * @param RedirectFactory $resultRedirect
     */
    public function __construct(
        Context $context,
        SMSProfileLogFactory $smsprofilelog,
        RedirectFactory $resultRedirect
    ) {
        parent::__construct($context);
        $this->resultRedirect = $resultRedirect;
        $this->smsprofilelog = $smsprofilelog;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ACTION_RESOURCE);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|
     * \Magento\Framework\App\ResponseInterface|
     * \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirect->create();
        $sms  = $this->smsprofilelog->create();
        try {
            $sms->smsProfileClearelog();
            $this->messageManager->addSuccessMessage(__('The Sms Log has been cleared.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $resultRedirect->setPath('*/*/');
    }
}
