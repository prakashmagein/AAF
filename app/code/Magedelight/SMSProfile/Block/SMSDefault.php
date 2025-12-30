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

namespace Magedelight\SMSProfile\Block;

use Magedelight\SMSProfile\Helper\Data;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class SMSDefault extends Template
{
    /** @var Data */
    private $dataHelper;

    /**
     * Constructor
     *
     * @param Context  $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $dataHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataHelper = $dataHelper;
    }

    /**
     * @return bool
     */
    public function getStatus()
    {
        if ($this->isCustomerCountry()) {
            return $this->dataHelper->getModuleStatus();
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isCustomerCountry()
    {
        return $this->dataHelper->isCustomerCountryEnabled();
    }

    /**
     * @return mixed
     */
    public function getDefaultCountry()
    {
        return $this->dataHelper->getDefaultCustomerCountry();
    }

    /**
     * @return mixed
     */
    public function getAvailableCountries()
    {
        return $this->dataHelper->getAvailableCountries();
    }
}
