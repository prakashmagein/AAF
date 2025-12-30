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


namespace Magedelight\SMSProfile\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magedelight\SMSProfile\Helper\Data as HelperData;

class RemoveBlock implements ObserverInterface
{


    /**  @var HelperData */
    private $datahelper;

    /**
     * Constructor
     *
     * @param HelperData $dataHelper
     */
    public function __construct(
        HelperData $dataHelper
    ) {
        $this->datahelper = $dataHelper;
    }

    /**
     * Execute
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $observer->getLayout();
        if ($this->datahelper->getModuleStatus() && $this->datahelper->loginPopupEnable()) {
            $block = $layout->getBlock('top.links');
            if ($block) {
                $layout->unsetElement('register-link');
            }
        }
    }
}
