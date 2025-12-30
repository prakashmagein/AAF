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
 
namespace Magedelight\SMSProfile\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magedelight\SMSProfile\Model\SMSLogFactory;
use Magedelight\SMSProfile\Helper\Data as HelperData;
use Magedelight\SMSProfile\Model\SMSNotificationService;

class Smslog extends \Magento\Backend\Block\Template
{
    /** @var SMSNotificationService */
    private $smsService;

    /** @var SMSLogFactory */
    private $smslog;

     /**  @var HelperData */
    private $datahelper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        HelperData $dataHelper,
        SMSNotificationService $smsService,
        SMSLogFactory $smslog
    ) {
        $this->smslog = $smslog;
        $this->smsService = $smsService;
        $this->datahelper = $dataHelper;
        parent::__construct($context);
    }

    public function getSMSDetailsById($id)
    {
        $sms = $this->smslog->create();
        $sms->load($id);
        return $sms;
    }
}
