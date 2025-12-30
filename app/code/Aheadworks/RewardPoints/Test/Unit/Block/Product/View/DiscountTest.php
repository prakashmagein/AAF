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
namespace Aheadworks\RewardPoints\Test\Unit\Block\Product\View;

use Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface;
use Aheadworks\RewardPoints\Block\Product\View\Discount;
use Aheadworks\RewardPoints\Model\Calculator\RateCalculator;
use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class Aheadworks\RewardPoints\Test\Unit\Block\Product\View\DiscountTest
 */
class DiscountTest extends TestCase
{
    /**
     * @var Context|MockObject
     */
    private $context;

    /**
     * @var CustomerRewardPointsManagementInterface|MockObject
     */
    private $customerRewardPointsService;

    /**
     * @var RateCalculator|MockObject
     */
    private $rateCalculator;

    /**
     * @var Session|MockObject
     */
    private $customerSession;

    /**
     * @var PriceHelper|MockObject
     */
    private $priceHelperMock;

    /**
     * @var Product|MockObject
     */
    private $productMock;

    /**
     * @var Discount
     */
    private $object;

    /**
     * @var RequestInterface|MockObject
     */
    private $requestMock;

    /**
     * @var ProductRepositoryInterface|MockObject
     */
    private $productRepositoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->customerRewardPointsService = $this->getMockBuilder(CustomerRewardPointsManagementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->rateCalculator = $this->getMockBuilder(RateCalculator::class)
            ->disableOriginalConstructor()
            ->setMethods(['calculateRewardDiscount'])
            ->getMock();

        $this->customerSession = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->productRepositoryMock = $this->getMockForAbstractClass(ProductRepositoryInterface::class);

        $this->priceHelperMock = $this->getMockBuilder(PriceHelper::class)
            ->disableOriginalConstructor()
            ->setMethods(['currency'])
            ->getMock();

        $this->productMock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPriceInfo'])
            ->getMock();

        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);
        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->willReturn(1);
        $this->context = $objectManager->getObject(
            Context::class,
            ['request' => $this->requestMock]
        );

        $data = [
            'context' => $this->context,
            'customerRewardPointsService' => $this->customerRewardPointsService,
            'rateCalculator' => $this->rateCalculator,
            'customerSession' => $this->customerSession,
            'productRepository' => $this->productRepositoryMock,
            'priceHelper' => $this->priceHelperMock
        ];

        $this->object = $objectManager->getObject(Discount::class, $data);
    }

    /**
     * Test template property
     */
    public function testTemplateProperty()
    {
        $class = new \ReflectionClass(Discount::class);
        $property = $class->getProperty('_template');
        $property->setAccessible(true);

        $this->assertEquals('Aheadworks_RewardPoints::product/view/discount.phtml', $property->getValue($this->object));
    }

    /**
     * Test getAvailablePoints method
     */
    public function testGetAvailablePoints()
    {
        $customerId = 3;
        $balance = 10;

        $this->customerSession->expects($this->any())
            ->method('getId')
            ->willReturn($customerId);

        $this->customerRewardPointsService->expects($this->once())
            ->method('getCustomerRewardPointsBalance')
            ->with($customerId)
            ->willReturn($balance);

        $this->assertEquals($balance, $this->object->getAvailablePoints());
    }

    /**
     * Test getAvailablePoints method
     */
    public function testGetAvailablePointsMethodNullCustomerValue()
    {
        $customerId = null;
        $balance = 0;

        $this->customerSession->expects($this->any())
            ->method('getId')
            ->willReturn($customerId);

        $this->assertEquals($balance, $this->object->getAvailablePoints());
    }

    /**
     * Test getAvailableAmount method
     */
    public function testGetAvailableAmount()
    {
        $customerId = 4;
        $balance = 12;
        $amount = 15;

        $this->customerSession->expects($this->any())
            ->method('getId')
            ->willReturn($customerId);

        $this->customerRewardPointsService->expects($this->once())
            ->method('getCustomerRewardPointsBalance')
            ->with($customerId)
            ->willReturn($balance);

        $this->rateCalculator->expects($this->once())
            ->method('calculateRewardDiscount')
            ->with($customerId, $balance)
            ->willReturn($amount);

        $class = new \ReflectionClass(Discount::class);
        $methodGetAvailableAmount = $class->getMethod('getAvailableAmount');
        $methodGetAvailableAmount->setAccessible(true);

        $this->assertEquals($amount, $methodGetAvailableAmount->invoke($this->object));
    }

    /**
     * Test getAvailableAmount method
     */
    public function testGetAvailableAmountMethodNullCustomerValue()
    {
        $customerId = null;
        $amount = 0;

        $this->customerSession->expects($this->any())
            ->method('getId')
            ->willReturn($customerId);

        $class = new \ReflectionClass(Discount::class);
        $methodGetAvailableAmount = $class->getMethod('getAvailableAmount');
        $methodGetAvailableAmount->setAccessible(true);

        $this->assertEquals($amount, $methodGetAvailableAmount->invoke($this->object));
    }

    /**
     * Test getFormattedAvailableAmount method
     */
    public function testGetFormattedAvailableAmountMethod()
    {
        $expectedValue = '$12.00';
        $customerId = 4;
        $balance = 12;
        $amount = 15;

        $this->customerSession->expects($this->any())
            ->method('getId')
            ->willReturn($customerId);

        $this->customerRewardPointsService->expects($this->once())
            ->method('getCustomerRewardPointsBalance')
            ->with($customerId)
            ->willReturn($balance);

        $this->rateCalculator->expects($this->once())
            ->method('calculateRewardDiscount')
            ->with($customerId, $balance)
            ->willReturn($amount);

        $this->priceHelperMock->expects($this->once())
            ->method('currency')
            ->with($amount, true, false)
            ->willReturn($expectedValue);

        $this->assertEquals($expectedValue, $this->object->getFormattedAvailableAmount());
    }
}
