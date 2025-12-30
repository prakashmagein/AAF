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
namespace Aheadworks\RewardPoints\Test\Unit\Observer;

use Aheadworks\RewardPoints\Observer\RedeemForOrder;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Quote\Model\Quote;
use Magento\Sales\Api\Data\OrderExtensionInterface;
use Magento\Sales\Model\Order;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class Aheadworks\RewardPoints\Test\Unit\Observer\RedeemForOrderTest
 */
class RedeemForOrderTest extends TestCase
{
    /**
     * @var RedeemForOrder
     */
    private $object;

    /**
     * @var Quote|MockObject
     */
    private $quoteMock;

    /**
     * @var Order|MockObject
     */
    private $orderMock;

    /**
     * @var Observer|MockObject
     */
    private $observerMock;

    /**
     * @var Event|MockObject
     */
    protected $eventMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->quoteMock = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getAwUseRewardPoints',
                    'getAwRewardPointsAmount',
                    'getBaseAwRewardPointsAmount',
                    'getAwRewardPoints',
                    'getAwRewardPointsDescription'
                ]
            )
            ->getMock();

        $orderExtensionAttributes = $this->getMockForAbstractClass(
            OrderExtensionInterface::class,
            [],
            '',
            true,
            true,
            true,
            ['getAwRewardPointsShippingAmount', 'getBaseAwRewardPointsShippingAmount', 'getAwRewardPointsShipping']
        );
        $this->orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'setAwUseRewardPoints',
                    'setAwRewardPointsAmount',
                    'setBaseAwRewardPointsAmount',
                    'setAwRewardPoints',
                    'setAwRewardPointsDescription',
                    'setAwRewardPointsShippingAmount',
                    'setBaseAwRewardPointsShippingAmount',
                    'setAwRewardPointsShipping',
                    'getExtensionAttributes'
                ]
            )
            ->getMock();
            $this->orderMock->expects($this->exactly(3))
                ->method('getExtensionAttributes')
                ->willReturn($orderExtensionAttributes);

        $this->eventMock = $this->getMockBuilder(Event::class)
            ->disableOriginalConstructor()
            ->setMethods(['getOrder', 'getQuote'])
            ->getMock();
        $this->eventMock->expects($this->any())
            ->method('getOrder')
            ->willReturn($this->orderMock);
        $this->eventMock->expects($this->any())
            ->method('getQuote')
            ->willReturn($this->quoteMock);

        $this->observerMock = $this->getMockBuilder(Observer::class)
            ->disableOriginalConstructor()
            ->setMethods(['getEvent'])
            ->getMock();
        $this->observerMock->expects($this->any())
            ->method('getEvent')
            ->willReturn($this->eventMock);

        $objectManager = new ObjectManager($this);
        $this->object = $objectManager->getObject(RedeemForOrder::class, []);
    }

    /**
     * Test execute method
     */
    public function testExecuteMethod()
    {
        $this->quoteMock->expects($this->exactly(2))
            ->method('getAwUseRewardPoints')
            ->willReturn(true);
        $this->quoteMock->expects($this->once())
            ->method('getAwRewardPointsAmount')
            ->willReturn(15);
        $this->quoteMock->expects($this->once())
            ->method('getBaseAwRewardPointsAmount')
            ->willReturn(15);
        $this->quoteMock->expects($this->once())
            ->method('getAwRewardPoints')
            ->willReturn(120);
        $this->quoteMock->expects($this->once())
            ->method('getAwRewardPointsDescription')
            ->willReturn('120 Reward Points');

        $this->orderMock->expects($this->once())
            ->method('setAwUseRewardPoints')
            ->with(true)
            ->willReturnSelf();
        $this->orderMock->expects($this->once())
            ->method('setAwRewardPointsAmount')
            ->with(15)
            ->willReturnSelf();
        $this->orderMock->expects($this->once())
            ->method('setBaseAwRewardPointsAmount')
            ->with(15)
            ->willReturnSelf();
        $this->orderMock->expects($this->once())
            ->method('setAwRewardPoints')
            ->with(120)
            ->willReturnSelf();
        $this->orderMock->expects($this->once())
            ->method('setAwRewardPointsDescription')
            ->with('120 Reward Points')
            ->willReturnSelf();

        $this->object->execute($this->observerMock);
    }
}
