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

use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\RawItemProcessor\CreditmemoItemsResolver;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\RawItemProcessor\OrderItemsResolver;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ItemFilter;
use Aheadworks\RewardPoints\Model\Calculator\Earning\Calculator\CreditMemo as CreditMemoCalculator;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Item as CreditmemoItem;
use Magento\Sales\Model\Order\Item;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\RawItemProcessor\CreditmemoItemsResolver
 */
class CreditmemoItemsResolverTest extends TestCase
{
    /**
     * @var CreditmemoItemsResolver
     */
    private $resolver;

    /**
     * @var MockObject|OrderItemsResolver
     */
    private MockObject|OrderItemsResolver $orderItemsResolverMock;

    /**
     * @var MockObject|CreditMemoCalculator
     */
    private MockObject|CreditMemoCalculator $creditMemoCalculator;

    /**
     * @var ItemFilter|MockObject
     */
    private MockObject|ItemFilter $itemFinder;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->orderItemsResolverMock = $this->createMock(OrderItemsResolver::class);
        $this->creditMemoCalculator = $this->createMock(CreditMemoCalculator::class);
        $this->itemFinder = $this->createMock(ItemFilter::class);

        $this->resolver = $objectManager->getObject(
            CreditmemoItemsResolver::class,
            [
                'orderItemsResolver' => $this->orderItemsResolverMock,
                'creditMemoCalculator' => $this->creditMemoCalculator,
                'itemFilter' => $this->itemFinder
            ]
        );
    }

    /**
     * Test getItems method
     *
     * @param OrderItemInterface[]|MockObject[] $orderItems
     * @param $creditmemoItems
     * @param $resultItems
     * @dataProvider getItemsDataProvider
     */
    public function testGetItems($orderItems, $creditmemoItems, $resultItems)
    {
        $orderId = 10;

        $creditmemoMock = $this->createMock(Creditmemo::class);
        $creditmemoMock->expects($this->once())
            ->method('getOrderId')
            ->willReturn($orderId);
        if (!empty($orderItems)) {
            $creditmemoMock->expects($this->once())
                ->method('getData')
                ->with('total_qty')
                ->willReturn(1.0);
            $creditmemoMock->expects($this->once())
                ->method('getItems')
                ->willReturn($creditmemoItems);

            $this->orderItemsResolverMock->expects($this->once())
                ->method('getOrderItems')
                ->with($orderId)
                ->willReturn($orderItems);

            $this->itemFinder->expects($this->once())
                ->method('filterItemsWithoutDiscount')
                ->with($resultItems)
                ->willReturn($resultItems);
        }

        $this->assertEquals($resultItems, $this->resolver->getItems($creditmemoMock));
    }

    /**
     * @return array
     */
    public function getItemsDataProvider()
    {
        $simpleOrderMock = $this->getOrderItemMock(null, 'simple', false);
        $parentOrderItemMock = $this->getOrderItemMock(null, 'configurable', true);
        $childOrderItemMock = $this->getOrderItemMock(11, 'simple', false);

        $simpleCreditmemoItemMock = $this->getCreditmemoItemMock(
            20,
            10,
            null,
            'simple',
            false,
            2
        );
        $parentCreditmemoItemMock = $this->getCreditmemoItemMock(
            21,
            11,
            null,
            'configurable',
            true,
            1
        );
        $childCreditmemoItemMock = $this->getCreditmemoItemMock(
            22,
            12,
            21,
            'simple',
            false,
            1
        );
        $emptyCreditmemoItemMock = $this->getCreditmemoItemMock(
            20,
            10,
            null,
            'simple',
            false,
            0
        );

        return [
            [
                'orderItems' => [
                    11 => $parentOrderItemMock,
                    12 => $childOrderItemMock,
                    10 => $simpleOrderMock
                ],
                'creditmemoItems' => [
                    $parentCreditmemoItemMock,
                    $childCreditmemoItemMock,
                    $simpleCreditmemoItemMock
                ],
                'resultItems' => [
                    21 => $parentCreditmemoItemMock,
                    22 => $childCreditmemoItemMock,
                    20 => $simpleCreditmemoItemMock
                ]
            ],
            [
                'orderItems' => [
                    11 => $parentOrderItemMock,
                    12 => $childOrderItemMock,
                    10 => $simpleOrderMock
                ],
                'creditmemoItems' => [
                    $parentCreditmemoItemMock,
                    $childCreditmemoItemMock,
                    $emptyCreditmemoItemMock
                ],
                'resultItems' => [
                    21 => $parentCreditmemoItemMock,
                    22 => $childCreditmemoItemMock
                ]
            ],
            [
                'orderItems' => [],
                'creditmemoItems' => [
                    $parentCreditmemoItemMock,
                    $childCreditmemoItemMock
                ],
                'resultItems' => []
            ]
        ];
    }

    /**
     * Get order item mock
     *
     * @param int|null $parentItemId
     * @param string $productType
     * @param bool $isChildrenCalculated
     * @return OrderItemInterface|MockObject
     */
    private function getOrderItemMock($parentItemId, $productType, $isChildrenCalculated)
    {
        $orderItemMock = $this->createMock(Item::class);
        $orderItemMock->expects($this->any())
            ->method('getParentItemId')
            ->willReturn($parentItemId);
        $orderItemMock->expects($this->any())
            ->method('getProductType')
            ->willReturn($productType);
        $orderItemMock->expects($this->any())
            ->method('isChildrenCalculated')
            ->willReturn($isChildrenCalculated);

        return $orderItemMock;
    }

    /**
     * Get creditmemo item mock
     *
     * @param int $id
     * @param int $orderItemId
     * @param int|null $parentItemId
     * @param string $productType
     * @param bool $isChildrenCalculated
     * @param int $qty
     * @return CreditmemoItem|MockObject
     */
    private function getCreditmemoItemMock(
        $id,
        $orderItemId,
        $parentItemId,
        $productType,
        $isChildrenCalculated,
        $qty
    ) {
        $creditmemoItemMock = $this->createPartialMock(
            CreditmemoItem::class,
            [
                'getEntityId',
                'getOrderItemId',
                'setItemId',
                'setParentItemId',
                'setProductType',
                'setIsChildrenCalculated',
                'getQty'
            ]
        );
        $creditmemoItemMock->expects($this->any())
            ->method('getEntityId')
            ->willReturn($id);
        $creditmemoItemMock->expects($this->any())
            ->method('getOrderItemId')
            ->willReturn($orderItemId);
        $creditmemoItemMock->expects($this->any())
            ->method('getQty')
            ->willReturn($qty);
        $creditmemoItemMock->expects($this->any())
            ->method('setItemId')
            ->with($id)
            ->willReturnSelf();
        $creditmemoItemMock->expects($this->any())
            ->method('setParentItemId')
            ->with($parentItemId)
            ->willReturnSelf();
        $creditmemoItemMock->expects($this->any())
            ->method('setProductType')
            ->with($productType)
            ->willReturnSelf();
        $creditmemoItemMock->expects($this->any())
            ->method('setIsChildrenCalculated')
            ->with($isChildrenCalculated)
            ->willReturnSelf();

        return $creditmemoItemMock;
    }
}
