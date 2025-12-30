<?php
/**
 * Magedelight
 * Copyright (C) 2023 Magedelight <info@magedelight.com>
 *
 * @category  Magedelight
 * @package   Magedelight_SMSProfile
 * @copyright Copyright (c) 2023 Mage Delight (http://www.magedelight.com/)
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author    Magedelight <info@magedelight.com>
 */

namespace Magedelight\SMSProfile\Block;

class MdPopup extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magedelight\SMSProfile\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magedelight\SMSProfile\Helper\Data $dataHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataHelper = $dataHelper;
        $this->customerSession = $customerSession;
        $this->jsonEncoder = $jsonEncoder;
    }

    /**
     * @return bool
     */
    public function isModuleEnabled()
    {
        return $this->dataHelper->getModuleStatus()
            && !$this->customerSession->isLoggedIn();
    }

    /**
     * @return bool
     */
    public function isPopupEnabled()
    {
        return $this->dataHelper->loginPopupEnable();
    }
}
