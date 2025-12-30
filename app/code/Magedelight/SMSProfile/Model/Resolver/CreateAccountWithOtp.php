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

declare(strict_types=1);

namespace Magedelight\SMSProfile\Model\Resolver;

use Magento\Authorization\Model\UserContextInterface;
use Magento\CustomerGraphQl\Model\Customer\ExtractCustomerData;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthenticationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magedelight\SMSProfile\Api\SMSProfieApiServicesInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Integration\Model\Oauth\TokenFactory as TokenModelFactory;

/**
 * Customers Token resolver, used for GraphQL request processing.
 */
class CreateAccountWithOtp implements ResolverInterface
{
    /**
     * @var SMSProfieApiServicesInterface
     */
    private $smsProfieApiServices;

    /**
     * @var PaymentInterfaceFactory
     */
    protected $customerInterfaceFactory;
    protected $tokenModelFactory;
    protected $extractCustomerData;

    /**
     * CreateAccountWithOtp constructor.
     * @param TokenModelFactory $tokenModelFactory
     * @param SMSProfieApiServicesInterface $smsProfieApiServices
     * @param CustomerInterfaceFactory $customerInterfaceFactory
     * @param ExtractCustomerData $extractCustomerData
     */
    public function __construct(
        TokenModelFactory $tokenModelFactory,
        SMSProfieApiServicesInterface $smsProfieApiServices,
        CustomerInterfaceFactory $customerInterfaceFactory,
        ExtractCustomerData $extractCustomerData
    ) {
        $this->tokenModelFactory = $tokenModelFactory;
        $this->smsProfieApiServices = $smsProfieApiServices;
        $this->extractCustomerData = $extractCustomerData;
        $this->customerInterfaceFactory = $customerInterfaceFactory;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!isset($args['input']) || !is_array($args['input']) || empty($args['input'])) {
            throw new GraphQlInputException(__('"input" value should be specified'));
        }

        if (!isset($args['password']) || empty($args['password'])) {
            throw new GraphQlInputException(__('Specify the "password" value.'));
        }

        if (!isset($args['mobile']) || empty($args['mobile'])) {
            throw new GraphQlInputException(__('Specify the "mobile" value.'));
        }

        if (!isset($args['otp']) || empty($args['otp'])) {
            throw new GraphQlInputException(__('Specify the "otp" value.'));
        }

        try {
            $data = [];
            $tokenKey = "";
            $customerData = $this->customerInterfaceFactory->create([ 'data' => $args['input'] ]);
            $customer = $this->smsProfieApiServices->createAccountWithOtp($customerData, $args['mobile'], $args['otp'], $args['password'], null);
            if ($customer->getId()) {
                $customerId = $customer->getId();
                $customerToken = $this->tokenModelFactory->create();
                $tokenKey = $customerToken->createCustomerToken($customerId)->getToken();
                $data = $this->extractCustomerData->execute($customer);
            }
            return ['customer' => $data, 'token' => $tokenKey];
        } catch (AuthenticationException $e) {
            throw new GraphQlAuthenticationException(__($e->getMessage()), $e);
        }
    }
}
