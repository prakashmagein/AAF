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
 
namespace Magedelight\SMSProfile\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magedelight\SMSProfile\Model\SMSProfileLogFactory;
use Magedelight\SMSProfile\Helper\Data as HelperData;

class SmsProfileLog extends Template
{
    /** @var SMSNotificationService */
    private $smsService;

    /** @var SMSProfileLogFactory */
    private $smsprofilelog;

    /**  @var HelperData */
    private $datahelper;

    /**
     * Construct
     *
     * @param Context $context
     * @param HelperData $dataHelper
     * @param SMSProfileLogFactory $smsprofilelog
     */
    public function __construct(
        Context $context,
        HelperData $dataHelper,
        SMSProfileLogFactory $smsprofilelog
    ) {
        $this->smsprofilelog = $smsprofilelog;
        $this->datahelper = $dataHelper;
        parent::__construct($context);
    }

    public function getSMSProfileDetailsById($id)
    {
        $sms = $this->smsprofilelog->create();
        $sms->load($id);
        return $sms;
    }
}
