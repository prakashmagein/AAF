<?php
/**
 * AccountManagement
 *
 * @copyright Copyright © 2020 Magepow. All rights reserved.
 * @author    @copyright Copyright (c) 2014 Magepow (<https://www.magepow.com>)
 * @license <https://www.magepow.com/license-agreement.html>
 * @Author: magepow<support@magepow.com>
 * @github: <https://github.com/magepow>
 */
namespace Magepow\OnestepCheckout\Plugin\Customer;

use Magento\Checkout\Model\Session;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\AccountManagement as AM;

class AccountManagement
{
    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * AccountManagement constructor.
     * @param Session $checkoutSession
     */
    public function __construct(Session $checkoutSession)
    {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param AM $subject
     * @param mixed $password
     * @param mixed $redirectUrl
     * @return mixed
     */
    public function beforeCreateAccount(AM $subject, CustomerInterface $customer, $password = null, $redirectUrl = '')
    {
        $data = $this->checkoutSession->getData();
        if (isset($data['register']) && $data['register'] && isset($data['password']) && $data['password']) {
            $password = $data['password'];
            return [$customer, $password, $redirectUrl];
        }
    }
}
