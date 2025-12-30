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
namespace Aheadworks\RewardPoints\Test\Unit\Model\Calculator\Earning\EarnItemResolver\ItemProcessor;

use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemInterface;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemInterfaceFactory;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ItemInterface;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ItemProcessor\Configurable as ConfigurableItemProcessor;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ItemProcessor\Configurable
 */
class ConfigurableTest extends TestCase
{
    /**
     * @var ConfigurableItemProcessor
     */
    private $processor;

    /**
     * @var EarnItemInterfaceFactory|MockObject
     */
    private $earnItemFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->earnItemFactoryMock = $this->createMock(EarnItemInterfaceFactory::class);

        $this->processor = $objectManager->getObject(
            ConfigurableItemProcessor::class,
            [
                'earnItemFactory' => $this->earnItemFactoryMock,
            ]
        );
    }

    /**
     * Test getEarnItem method
     *
     * @param array $groupedItems
     * @param bool $beforeTax
     * @param EarnItemInterface|MockObject $earnItem
     * @dataProvider getEarnItemDataProvider
     */
    public function testGetEarnItem($groupedItems, $beforeTax, $earnItem)
    {
        $this->earnItemFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($earnItem);

        $this->assertSame($earnItem, $this->processor->getEarnItem($groupedItems, $beforeTax));
    }

    /**
     * @return array
     */
    public function getEarnItemDataProvider()
    {
        $parentItem = $this->getItemMock(null, 125, 100, 110, 25, 10, 5);
        $childItem = $this->getItemMock($parentItem, 126, 0, 0, 0, 0, 0);
        return [
            [
                'groupedItems' => [$parentItem, $childItem],
                'beforeTax' => true,
                'earnItem' => $this->getEarnItemMock(126, 65, 5)
            ],
            [
                'groupedItems' => [$parentItem, $childItem],
                'beforeTax' => false,
                'earnItem' => $this->getEarnItemMock(126, 75, 5)
            ],
            [
                'groupedItems' => [$parentItem],
                'beforeTax' => false,
                'earnItem' => $this->getEarnItemMock(null, 0, 0)
            ],
            [
                'groupedItems' => [$childItem],
                'beforeTax' => false,
                'earnItem' => $this->getEarnItemMock(null, 0, 0)
            ],
            [
                'groupedItems' => [],
                'beforeTax' => false,
                'earnItem' => $this->getEarnItemMock(null, 0, 0)
            ],
        ];
    }

    /**
     * Get item mock
     *
     * @param ItemInterface|MockObject|null $parent
     * @param int $productId
     * @param float $baseRowTotal
     * @param float $baseRowTotalInclTax
     * @param float $baseDiscountAmount
     * @param float $baseAwRewardPintsAmount
     * @param float $qty
     * @return ItemInterface|MockObject
     */
    private function getItemMock(
        $parent,
        $productId,
        $baseRowTotal,
        $baseRowTotalInclTax,
        $baseDiscountAmount,
        $baseAwRewardPintsAmount,
        $qty
    ) {
        $itemMock = $this->createMock(ItemInterface::class);
        $itemMock->expects($this->any())
            ->method('getParentItem')
            ->willReturn($parent);
        $itemMock->expects($this->any())
            ->method('getProductId')
            ->willReturn($productId);
        $itemMock->expects($this->any())
            ->method('getBaseRowTotal')
            ->willReturn($baseRowTotal);
        $itemMock->expects($this->any())
            ->method('getBaseRowTotalInclTax')
            ->willReturn($baseRowTotalInclTax);
        $itemMock->expects($this->any())
            ->method('getBaseDiscountAmount')
            ->willReturn($baseDiscountAmount);
        $itemMock->expects($this->any())
            ->method('getBaseAwRewardPointsAmount')
            ->willReturn($baseAwRewardPintsAmount);
        $itemMock->expects($this->any())
            ->method('getQty')
            ->willReturn($qty);

        return $itemMock;
    }

    /**
     * Get earn item mock
     *
     * @param int $productId
     * @param float $baseAmount
     * @param $qty
     * @return EarnItemInterface|MockObject
     */
    private function getEarnItemMock($productId, $baseAmount, $qty)
    {
        $earnItemMock = $this->createMock(EarnItemInterface::class);
        $earnItemMock->expects($this->once())
            ->method('setProductId')
            ->with($productId)
            ->willReturnSelf();
        $earnItemMock->expects($this->once())
            ->method('setBaseAmount')
            ->with($baseAmount)
            ->willReturnSelf();
        $earnItemMock->expects($this->once())
            ->method('setQty')
            ->with($qty)
            ->willReturnSelf();

        return $earnItemMock;
    }
}
