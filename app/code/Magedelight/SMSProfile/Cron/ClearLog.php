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
 
namespace Magedelight\SMSProfile\Cron;

use Magedelight\SMSProfile\Model\SMSLogFactory;
use Magedelight\SMSProfile\Helper\Data as HelperData;

class ClearLog
{
    /**
     * @var SMSLogFactory
     */
    private $smslog;

    /**
     * @var HelperData
     */
    private $datahelper;

    /**
     * @param SMSLogFactory $smslog
     * @param HelperData $dataHelper
     */
    public function __construct(
        HelperData $dataHelper,
        SMSLogFactory $smslog
    ) {
        $this->smslog = $smslog;
        $this->datahelper = $dataHelper;
    }

    /**
     * SmsLog clear for Cron request
     *
     * @return RedirectFactory
     */
    public function execute()
    {
        if ($this->datahelper->getSmsLogEnable() && $this->datahelper->getCronStatus()) {
            $sms  = $this->smslog->create();
            try {
                $sms->clearelog();
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        return $this;
    }
}
