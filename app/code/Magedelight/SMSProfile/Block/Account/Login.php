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

namespace Magedelight\SMSProfile\Block\Account;

class Login extends \Magento\Customer\Block\Form\Login
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $requestInterface;

    /**
     * Login constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Url $customerUrl,
        array $data = []
    ) {
        $this->requestInterface = $context->getRequest();
        parent::__construct($context, $customerSession, $customerUrl, $data);
    }

    /**
     * Prepare layout
     *
     * @return $this|Login
     */
    public function _prepareLayout()
    {
        $routeName      = $this->requestInterface->getRouteName();
        $controllerName = $this->requestInterface->getControllerName();
        $actionName     = $this->requestInterface->getActionName();
        if ($routeName == 'customer' && $controllerName == 'account' && $actionName == 'login') {
            $this->pageConfig->getTitle()->set(__('Customer Login'));
        }
        return $this;
    }
}
