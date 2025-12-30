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

class View extends \Magento\Backend\App\Action
{

    /**
    * @var string
    */
    const ACTION_RESOURCE = 'Magedelight_SMSProfile::smslog';

     /**
      * @var \Magento\Framework\View\Result\PageFactory
      */
    private $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ACTION_RESOURCE);
    }

    /**
     * SmsLog view for AJAX request
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magedelight_SMSProfile::smslog');
        $resultPage->addBreadcrumb(__('Magedelight'), __('Magedelight'));
        $resultPage->addBreadcrumb(__('Notification SMSLog'), __('Notification SMSLog'));
        $resultPage->getConfig()->getTitle()->prepend((__('Notification SMSLog View')));
        return $resultPage;
    }
}
