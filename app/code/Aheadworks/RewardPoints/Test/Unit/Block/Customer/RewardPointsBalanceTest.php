<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://aheadworks.com/end-user-license-agreement/
 *
 * @package    RewardPoints
 * @version    2.4.0
 * @copyright  Copyright (c) 2024 Aheadworks Inc. (https://aheadworks.com/)
 * @license    https://aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\RewardPoints\Test\Unit\Block\Customer;

use Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface;
use Aheadworks\RewardPoints\Block\Customer\RewardPointsBalance;
use Aheadworks\RewardPoints\Model\Config;
use Magento\Backend\Block\Template\Context;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class Aheadworks\RewardPoints\Test\Unit\Block\Customer\RewardPointsBalanceTest
 */
class RewardPointsBalanceTest extends TestCase
{
    /**
     * @var \Aheadworks\RewardPoints\Block\Customer\Toplink\RewardPointsBalance
     */
    private $object;

    /**
     * @var Config|MockObject
     */
    private $configMock;

    /**
     * @var CustomerRewardPointsManagementInterface|MockObject
     */
    private $customerRewardPointsServiceMock;

    /**
     * @var CurrentCustomer|MockObject
     */
    private $currentCustomerMock;

    /**
     * @var PriceHelper|MockObject
     */
    private $priceHelperMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->configMock = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->setMethods(['isPointsBalanceTopLinkAtFrontend'])
            ->getMockForAbstractClass();

        $this->customerRewardPointsServiceMock = $this->getMockBuilder(
            CustomerRewardPointsManagementInterface::class
        )
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getCustomerRewardPointsBalance',
                    'getCustomerRewardPointsBalanceBaseCurrency',
                ]
            )
            ->getMockForAbstractClass();

        $this->currentCustomerMock = $this->getMockBuilder(CurrentCustomer::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCustomerId'])
            ->getMockForAbstractClass();

        $this->priceHelperMock = $this->getMockBuilder(PriceHelper::class)
            ->disableOriginalConstructor()
            ->setMethods(['currency'])
            ->getMock();

        $data = [
            'context' => $contextMock,
            'config' => $this->configMock,
            'customerRewardPointsService' => $this->customerRewardPointsServiceMock,
            'currentCustomer' => $this->currentCustomerMock,
            'priceHelper' => $this->priceHelperMock,
        ];

        $this->object = $objectManager->getObject(RewardPointsBalance::class, $data);
    }

    /**
     * Test getCustomerRewardPointsBalance method
     */
    public function testGetCustomerRewardPointsBalanceMethod()
    {
        $expectedValue = 88;
        $customerId = 3;

        $this->currentCustomerMock->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $this->customerRewardPointsServiceMock->expects($this->once())
            ->method('getCustomerRewardPointsBalance')
            ->willReturn($expectedValue);

        $this->assertEquals($expectedValue, $this->object->getCustomerRewardPointsBalance());
    }

    /**
     * Test getFormattedCustomerBalanceCurrency method
     */
    public function testGetFormattedCustomerBalanceCurrencyMethod()
    {
        $expectedValue = 88.00;
        $customerId = 3;

        $this->currentCustomerMock->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $this->customerRewardPointsServiceMock->expects($this->once())
            ->method('getCustomerRewardPointsBalanceBaseCurrency')
            ->willReturn($expectedValue);

        $this->priceHelperMock->expects($this->once())
            ->method('currency')
            ->with($expectedValue, true, false)
            ->willReturn($expectedValue);

        $this->assertEquals($expectedValue, $this->object->getFormattedCustomerBalanceCurrency());
    }
}
