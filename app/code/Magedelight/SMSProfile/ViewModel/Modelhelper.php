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

namespace Magedelight\SMSProfile\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magedelight\SMSProfile\Helper\Data;
use Magento\Customer\Helper\Address;
use Magento\Contact\Helper\Data as contactHelper;

class Modelhelper implements ArgumentInterface
{
    protected $helper;
    protected $addressHelper;
    protected $contactHelper;


    public function __construct(
        Data $helperData,
        Address $addressHelper,
        contactHelper $contactHelper
    ) {
          $this->helper = $helperData;
          $this->addressHelper = $addressHelper;
          $this->contactHelper = $contactHelper;
    }

    public function getHelperData()
    {
        return $this->helper;
    }

    public function getAddressHelper()
    {
        return $this->addressHelper;
    }

    public function getContactHelper()
    {
        return $this->contactHelper;
    }
}
