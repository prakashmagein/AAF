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

namespace Magedelight\SMSProfile\Block\Account\Edit;

class Form extends \Magento\Framework\View\Element\Template
{
    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $session
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\ProductMetadataInterface $productmetadata,
        \Magento\Customer\Model\Session $session
    ) {
        $this->session = $session;
        $this->productmetadata = $productmetadata;
        parent::__construct($context);
    }

    public function getMobile()
    {
        if ($this->session->isLoggedIn()) {
            if ($this->session->getCustomer()->getCustomerMobile()) {
                return $this->session->getCustomer()->getCustomerMobile();
            }
            return '';
        }
        return '';
    }

    public function getCountryCode()
    {
        if ($this->session->isLoggedIn()) {
            if ($this->session->getCustomer()->getCountryreg()) {
                return $this->session->getCustomer()->getCountryreg();
            }
            return '';
        }
        return '';
    }

    public function getMagentoEdition()
    {
        $magentoVersion = $this->productmetadata;
        return $magentoVersion->getEdition();
    }
}
