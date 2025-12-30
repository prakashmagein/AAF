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
namespace Aheadworks\RewardPoints\Test\Unit\Model\Calculator\Earning\EarnItemResolver\RawItemProcessor;

use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\RawItemProcessor\OrderItemsResolver;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\RawItemProcessor\OrderItemsResolver
 */
class OrderItemsResolverTest extends TestCase
{
    /**
     * @var OrderItemsResolver
     */
    private $resolver;

    /**
     * @var OrderRepositoryInterface|MockObject
     */
    private $orderRepositoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->orderRepositoryMock = $this->createMock(OrderRepositoryInterface::class);

        $this->resolver = $objectManager->getObject(
            OrderItemsResolver::class,
            [
                'orderRepository' => $this->orderRepositoryMock,
            ]
        );
    }

    /**
     * Test getOrderItems method
     *
     * @param OrderItemInterface[]|MockObject[] $orderItems
     * @param OrderItemInterface[]|MockObject[] $result
     * @dataProvider getOrderItemsDataProvider
     */
    public function testGetOrderItems($orderItems, $result)
    {
        $orderId = 125;
        $orderMock = $this->createMock(OrderInterface::class);

        $this->orderRepositoryMock->expects($this->once())
            ->method('get')
            ->with($orderId)
            ->willReturn($orderMock);

        $orderMock->expects($this->once())
            ->method('getItems')
            ->willReturn($orderItems);

        $this->assertEquals($result, $this->resolver->getOrderItems($orderId));
    }

    /**
     * @return array
     */
    public function getOrderItemsDataProvider()
    {
        $orderItemFirstMock = $this->getOrderItemMock(201);
        $orderItemSecondMock =  $this->getOrderItemMock(202);
        return [
            [
                'orderItems' => [
                    $orderItemFirstMock,
                    $orderItemSecondMock
                ],
                'result' => [
                    201 => $orderItemFirstMock,
                    202 => $orderItemSecondMock
                ]
            ],
            [
                'orderItems' => [
                    $orderItemSecondMock
                ],
                'result' => [
                    202 => $orderItemSecondMock
                ]
            ],
            [
                'orderItems' => [],
                'result' => []
            ]
        ];
    }

    /**
     * Test getOrderItems method if no order found
     */
    public function testGetOrderItemsNoOrder()
    {
        $orderId = 125;

        $this->orderRepositoryMock->expects($this->once())
            ->method('get')
            ->with($orderId)
            ->willThrowException(new NoSuchEntityException(__('No such entity!')));

        $this->assertEquals([], $this->resolver->getOrderItems($orderId));
    }

    /**
     * Get order item mock
     *
     * @param int $itemId
     * @return OrderItemInterface|MockObject
     */
    private function getOrderItemMock($itemId)
    {
        $orderItemMock = $this->createMock(OrderItemInterface::class);
        $orderItemMock->expects($this->any())
            ->method('getItemId')
            ->willReturn($itemId);

        return $orderItemMock;
    }
}
