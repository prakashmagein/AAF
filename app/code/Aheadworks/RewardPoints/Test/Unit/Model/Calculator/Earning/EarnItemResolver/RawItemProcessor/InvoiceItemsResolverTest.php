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

use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\RawItemProcessor\InvoiceItemsResolver;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\RawItemProcessor\OrderItemsResolver;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ItemFilter;
use Aheadworks\RewardPoints\Model\Calculator\Earning\Calculator\Invoice as InvoiceCalculator;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use Magento\Sales\Model\Order\Item;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\RawItemProcessor\InvoiceItemsResolver
 */
class InvoiceItemsResolverTest extends TestCase
{
    /**
     * @var InvoiceItemsResolver
     */
    private $resolver;

    /**
     * @var MockObject|OrderItemsResolver
     */
    private MockObject|OrderItemsResolver $orderItemsResolverMock;

    /**
     * @var MockObject|InvoiceCalculator
     */
    private MockObject|InvoiceCalculator $invoiceCalculator;

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
        $this->invoiceCalculator = $this->createMock(InvoiceCalculator::class);
        $this->itemFinder = $this->createMock(ItemFilter::class);

        $this->resolver = $objectManager->getObject(
            InvoiceItemsResolver::class,
            [
                'orderItemsResolver' => $this->orderItemsResolverMock,
                'creditMemoCalculator' => $this->invoiceCalculator,
                'itemFilter' => $this->itemFinder
            ]
        );
    }

    /**
     * Test getItems method
     *
     * @param OrderItemInterface[]|MockObject[] $orderItems
     * @param InvoiceItem[]|MockObject[] $invoiceItems
     * @param InvoiceItem[]|MockObject[] $resultItems
     * @dataProvider getItemsDataProvider
     */
    public function testGetItems($orderItems, $invoiceItems, $resultItems)
    {
        $orderId = 10;
        $invoiceMock = $this->createMock(InvoiceInterface::class);
        $invoiceMock->expects($this->once())
            ->method('getOrderId')
            ->willReturn($orderId);

        if (!empty($orderItems)) {
            $invoiceMock->expects($this->once())
                ->method('getItems')
                ->willReturn($invoiceItems);

            $this->orderItemsResolverMock->expects($this->once())
                ->method('getOrderItems')
                ->with($orderId)
                ->willReturn($orderItems);
        }

        $this->itemFinder->expects($this->once())
            ->method('filterItemsWithoutDiscount')
            ->with($resultItems)
            ->willReturn($resultItems);

        $this->assertEquals($resultItems, $this->resolver->getItems($invoiceMock));
    }

    /**
     * @return array
     */
    public function getItemsDataProvider()
    {
        $simpleOrderMock = $this->getOrderItemMock(null, 'simple', false);
        $parentOrderItemMock = $this->getOrderItemMock(null, 'configurable', true);
        $childOrderItemMock = $this->getOrderItemMock(11, 'simple', false);

        $simpleInvoiceItemMock = $this->getInvoiceItemMock(20, 10, null, 'simple', false);
        $parentInvoiceItemMock = $this->getInvoiceItemMock(21, 11, null, 'configurable', true);
        $childInvoiceItemMock = $this->getInvoiceItemMock(22, 12, 21, 'simple', false);

        return [
            [
                'orderItems' => [
                    11 => $parentOrderItemMock,
                    12 => $childOrderItemMock,
                    10 => $simpleOrderMock
                ],
                'invoiceItems' => [
                    $parentInvoiceItemMock,
                    $childInvoiceItemMock,
                    $simpleInvoiceItemMock
                ],
                'resultItems' => [
                    21 => $parentInvoiceItemMock,
                    22 => $childInvoiceItemMock,
                    20 => $simpleInvoiceItemMock
                ]
            ],
            [
                'orderItems' => [],
                'invoiceItems' => [
                    $parentInvoiceItemMock,
                    $childInvoiceItemMock
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
     * Get invoice item mock
     *
     * @param int $id
     * @param int $orderItemId
     * @param int|null $parentItemId
     * @param string $productType
     * @param bool $isChildrenCalculated
     * @return InvoiceItem|MockObject
     */
    private function getInvoiceItemMock($id, $orderItemId, $parentItemId, $productType, $isChildrenCalculated)
    {
        $invoiceItemMock = $this->createPartialMock(
            InvoiceItem::class,
            [
                'getEntityId',
                'getOrderItemId',
                'setItemId',
                'setParentItemId',
                'setProductType',
                'setIsChildrenCalculated'
            ]
        );
        $invoiceItemMock->expects($this->any())
            ->method('getEntityId')
            ->willReturn($id);
        $invoiceItemMock->expects($this->any())
            ->method('getOrderItemId')
            ->willReturn($orderItemId);
        $invoiceItemMock->expects($this->once())
            ->method('setItemId')
            ->with($id)
            ->willReturnSelf();
        $invoiceItemMock->expects($this->once())
            ->method('setParentItemId')
            ->with($parentItemId)
            ->willReturnSelf();
        $invoiceItemMock->expects($this->once())
            ->method('setProductType')
            ->with($productType)
            ->willReturnSelf();
        $invoiceItemMock->expects($this->once())
            ->method('setIsChildrenCalculated')
            ->with($isChildrenCalculated)
            ->willReturnSelf();

        return $invoiceItemMock;
    }
}
