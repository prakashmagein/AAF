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
namespace Aheadworks\RewardPoints\Test\Unit\Block\Sales\Order;

use Aheadworks\RewardPoints\Block\Sales\Order\Total;
use Aheadworks\RewardPoints\Model\Config;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\Factory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\LayoutInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\Invoice;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class Aheadworks\RewardPoints\Test\Unit\Block\Sales\Order\TotalTest
 */
class TotalTest extends TestCase
{
    /**
     * @var AbstractBlock|MockObject
     */
    private $abstractBlockMock;

    /**
     * @var Context|MockObject
     */
    private $contextMock;

    /**
     * @var LayoutInterface|MockObject
     */
    private $layoutMock;

    /**
     * @var Factory|MockObject
     */
    private $factoryMock;

    /**
     * @var Config|MockObject
     */
    private $configMock;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerMock;

    /**
     * @var OrderInterface|MockObject
     */
    private $orderMock;

    /**
     * @var Invoice|MockObject
     */
    private $invoiceMock;

    /**
     * @var Total
     */
    private $object;

    /**
     * @var string
     */
    private $nameInLayout = 'aw_reward_points.sales.order.total';

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->abstractBlockMock = $this->getMockBuilder(Factory::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSource', 'getOrder', 'addTotal'])
            ->getMockForAbstractClass();

        $this->orderMock = $this->getMockBuilder(OrderInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getAwUseRewardPoints',
                ]
            )
            ->getMockForAbstractClass();

        $this->invoiceMock = $this->getMockBuilder(Invoice::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getAwRewardPointsDescription',
                    'getAwRewardPointsAmount',
                ]
            )
            ->getMock();

        $this->prepareContext();

        $this->factoryMock = $this->getMockBuilder(Factory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->configMock = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->setMethods(['getLabelNameRewardPoints'])
            ->getMock();
        $this->storeManagerMock = $this->getMockBuilder(StoreManagerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getWebsite'])
            ->getMockForAbstractClass();

        $data = [
            'context' => $this->contextMock,
            'factory' => $this->factoryMock,
            'config' => $this->configMock
        ];

        $this->object = $objectManager->getObject(Total::class, $data);
    }

    /**
     * Prepare context mock
     */
    private function prepareContext()
    {
        $this->contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getLayout',
                    'getStoreManager'
                ]
            )
            ->getMock();

        $this->layoutMock = $this->getMockBuilder(LayoutInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getParentName',
                    'getBlock',
                ]
            )
            ->getMockForAbstractClass();

        $this->contextMock->expects($this->once())
            ->method('getLayout')
            ->willReturn($this->layoutMock);
    }

    /**
     * Test getOrder method for null parent block
     */
    public function testGetOrderMethodFalseParentBlock()
    {
        $this->expectsParentBlockFalse();

        $this->assertNull($this->object->getOrder());
    }

    /**
     * Test getOrder method
     */
    public function testGetOrderMethod()
    {
        $this->expectsParentBlock('order_totals');

        $this->abstractBlockMock->expects($this->once())
            ->method('getOrder')
            ->willReturn($this->orderMock);

        $this->assertSame($this->orderMock, $this->object->getOrder());
    }

    /**
     * Test getSource method for null parent block
     */
    public function testGetSourceMethodFalseParentBlock()
    {
        $this->expectsParentBlockFalse();

        $this->assertNull($this->object->getSource());
    }

    /**
     * Test getSource method
     */
    public function testGetSourceMethod()
    {
        $this->expectsParentBlock('order_totals');

        $this->abstractBlockMock->expects($this->once())
            ->method('getSource')
            ->willReturn($this->invoiceMock);

        $this->assertSame($this->invoiceMock, $this->object->getSource());
    }

    /**
     * Test initTotals for null parent block
     */
    public function testInitTotalMethodFalseParentBlock()
    {
        $this->expectsParentBlockFalse();

        $this->object->initTotals();
    }

    /**
     * Test initTotals for null getOrder
     */
    public function testInitTotalMethodNullGetOrder()
    {
        $this->expectsParentBlock('order_totals');

        $this->abstractBlockMock->expects($this->once())
            ->method('getOrder')
            ->willReturn(null);

        $this->object->initTotals();
    }

    /**
     * Test initTotals for null getSource
     */
    public function testInitTotalMethodNullGetSource()
    {
        $this->expectsParentBlockTwice('order_totals');

        $this->abstractBlockMock->expects($this->once())
            ->method('getOrder')
            ->willReturn($this->orderMock);

        $this->abstractBlockMock->expects($this->once())
            ->method('getSource')
            ->willReturn(null);

        $this->object->initTotals();
    }

    /**
     * Test initTotals method
     */
    public function testInitTotalMethod()
    {
        $label = 'Reward Points';
        $value = 16;

        $this->expectsParentBlockThreeTimes('order_totals');

        $this->abstractBlockMock->expects($this->once())
            ->method('getOrder')
            ->willReturn($this->orderMock);
        $storeMock = $this->getMockBuilder(StoreInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMockForAbstractClass();
        $this->storeManagerMock->expects($this->once())
            ->method('getWebsite')
            ->willReturn($storeMock);
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn(1);
        $this->contextMock->expects($this->once())
            ->method('getStoreManager')
            ->willReturn($this->storeManagerMock);
        $this->configMock->expects($this->never())
            ->method('getLabelNameRewardPoints')
            ->willReturn($label);

        $this->orderMock->expects($this->once())
            ->method('getAwUseRewardPoints')
            ->willReturn(true);

        $this->abstractBlockMock->expects($this->once())
            ->method('getSource')
            ->willReturn($this->invoiceMock);

        $this->invoiceMock->expects($this->once())
            ->method('getAwRewardPointsDescription')
            ->willReturn($label);

        $this->invoiceMock->expects($this->once())
            ->method('getAwRewardPointsAmount')
            ->willReturn($value);

        $dataObjectMock = $this->getMockBuilder(DataObject::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $this->factoryMock->expects($this->once())
            ->method('create')
            ->with(
                [
                    'code' => 'aw_reward_points',
                    'strong' => false,
                    'label' => $label,
                    'value' =>  $value,
                ]
            )
            ->willReturn($dataObjectMock);

        $this->abstractBlockMock->expects($this->once())
            ->method('addTotal')
            ->with($dataObjectMock)
            ->willReturnSelf();

        $this->object->initTotals();
    }

    /**
     * Expects getParentBlock method
     *
     * @param string $parentName
     * @return void
     */
    private function expectsParentBlock($parentName)
    {
        $this->object->setNameInLayout($this->nameInLayout);

        $this->layoutMock->expects($this->once())
            ->method('getParentName')
            ->with($this->nameInLayout)
            ->willReturn($parentName);
        $this->layoutMock->expects($this->once())
            ->method('getBlock')
            ->with($parentName)
            ->willReturn($this->abstractBlockMock);
    }

    /**
     * Expects getParentBlock method exactly 2
     *
     * @param string $parentName
     * @return void
     */
    private function expectsParentBlockTwice($parentName)
    {
        $this->object->setNameInLayout($this->nameInLayout);

        $this->layoutMock->expects($this->exactly(2))
            ->method('getParentName')
            ->with($this->nameInLayout)
            ->willReturn($parentName);
        $this->layoutMock->expects($this->exactly(2))
            ->method('getBlock')
            ->with($parentName)
            ->willReturn($this->abstractBlockMock);
    }

    /**
     * Expects getParentBlock method exactly 3
     *
     * @param string $parentName
     * @return void
     */
    private function expectsParentBlockThreeTimes($parentName)
    {
        $this->object->setNameInLayout($this->nameInLayout);

        $this->layoutMock->expects($this->exactly(3))
            ->method('getParentName')
            ->with($this->nameInLayout)
            ->willReturn($parentName);
        $this->layoutMock->expects($this->exactly(3))
            ->method('getBlock')
            ->with($parentName)
            ->willReturn($this->abstractBlockMock);
    }

    /**
     * Expects getParentBlock method, return null parentName
     *
     * @return void
     */
    private function expectsParentBlockFalse()
    {
        $this->object->setNameInLayout($this->nameInLayout);

        $this->layoutMock->expects($this->once())
            ->method('getParentName')
            ->with($this->nameInLayout)
            ->willReturn(null);
    }
}
