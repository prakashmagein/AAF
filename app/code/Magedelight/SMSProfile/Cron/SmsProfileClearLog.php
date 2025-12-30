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

namespace Magedelight\SMSProfile\Cron;

use Magedelight\SMSProfile\Helper\Data as HelperData;
use Magedelight\SMSProfile\Model\SMSProfileLogFactory;
use Magento\Framework\Message\ManagerInterface;

class SmsProfileClearLog
{

    /** @var SMSProfileLogFactory */
    private $smslog;

    /**  @var HelperData */
    private $datahelper;

    /** @var ManagerInterface */
    private $messageManager;

    /**
     * @param HelperData $dataHelper
     * @param SMSProfileLogFactory $smslog
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        HelperData $dataHelper,
        SMSProfileLogFactory $smslog,
        ManagerInterface $messageManager
    ) {
        $this->smslog = $smslog;
        $this->datahelper = $dataHelper;
        $this->messageManager = $messageManager;
    }

    /**
     * @return $this
     */
    public function execute()
    {
        if ($this->datahelper->getSmsProfileLogStatus() && $this->datahelper->getSmsProfileCronStatus()) {
            $sms  = $this->smslog->create();
            try {
                $sms->smsProfileClearelog();
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        return $this;
    }
}
