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

namespace Magedelight\SMSProfile\Plugin;

use Magedelight\SMSProfile\Helper\Data as HelperData;
use Magento\Framework\Encryption\EncryptorInterface;

class CustomerRepository
{

    /**  @var HelperData */
    private $datahelper;

    protected $encryptor;

    public function __construct(HelperData $dataHelper,EncryptorInterface $encryptor,)
    {
        $this->datahelper = $dataHelper;
        $this->encryptor = $encryptor;
    }

     /**
      * Before customer save.
      *
      * @param CustomerRepositoryInterface $customerRepository
      * @param CustomerInterface $customer
      * @param null $passwordHash
      * @return array
      *
      */
    public function beforeSave(
        $subject,
        \Magento\Customer\Api\Data\CustomerInterface $customer,
        $passwordHash = null
    ) {
        if ($this->datahelper->getModuleStatus() && $this->datahelper->getSmsProfileEmailOptionalOnSignUp()) {
            if ($customer->getEmail()== null) {
                $email = $customer->getCustomAttribute('customer_mobile')->getValue()."@".$this->datahelper->getStoreDomain();
                $customer->setEmail($email);
                $customer->setConfirmation(null);
                //$customer->setStatus(1);

                $randomPassword = $this->generateRandomPassword();
                $passwordHash = $this->encryptor->getHash($randomPassword, true);
            }
        }


        return [$customer, $passwordHash];
    }

    protected function generateRandomPassword($length = 10)
    {
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
    }
}
