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
namespace Aheadworks\RewardPoints\Test\Unit\Model\Calculator\Earning\EarnItemResolver;

use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemInterface;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ItemInterface;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ItemProcessor;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ItemProcessorInterface;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ItemProcessorPool;
use Magento\Framework\Exception\ConfigurationMismatchException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ItemProcessor
 */
class ItemProcessorTest extends TestCase
{
    /**
     * @var ItemProcessor
     */
    private $processor;

    /**
     * @var ItemProcessorPool|MockObject
     */
    public $processorPoolMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->processorPoolMock = $this->createMock(ItemProcessorPool::class);

        $this->processor = $objectManager->getObject(
            ItemProcessor::class,
            [
                'processorPool' => $this->processorPoolMock
            ]
        );
    }

    /**
     * Test getEarnItem method
     *
     * @param $items
     * @param $productType
     * @param $beforeTax
     * @throws \Exception
     * @dataProvider getEarnItemDataProvider
     */
    public function testGetEarnItem($items, $productType, $beforeTax)
    {
        $earnItemMock = $this->createMock(EarnItemInterface::class);

        $itemProcessorMock = $this->createMock(ItemProcessorInterface::class);

        $this->processorPoolMock->expects($this->once())
            ->method('getProcessorByCode')
            ->with($productType)
            ->willReturn($itemProcessorMock);

        $itemProcessorMock->expects($this->once())
            ->method('getEarnItem')
            ->with($items, $beforeTax)
            ->willReturn($earnItemMock);

        $this->assertSame($earnItemMock, $this->processor->getEarnItem($items, $beforeTax));
    }

    /**
     * @return array
     */
    public function getEarnItemDataProvider()
    {
        $configurableParentMock = $this->getItemMock('configurable', null);
        $configurableChildMock = $this->getItemMock('simple', $configurableParentMock);

        return [
            [
                'items' => [],
                'productType' => null,
                'beforeTax' => true
            ],
            [
                'items' => [],
                'productType' => null,
                'beforeTax' => false
            ],
            [
                'items' => [$this->getItemMock('simple', null)],
                'productType' => 'simple',
                'beforeTax' => true
            ],
            [
                'items' => [$this->getItemMock('simple', null)],
                'productType' => 'simple',
                'beforeTax' => false
            ],
            [
                'items' => [$configurableParentMock, $configurableChildMock],
                'productType' => 'configurable',
                'beforeTax' => true
            ],
            [
                'items' => [$configurableParentMock, $configurableChildMock],
                'productType' => 'configurable',
                'beforeTax' => false
            ],
        ];
    }

    /**
     * Test getEarnItem method if an exception occurs
     */
    public function testGetEarnItemException()
    {
        $items = [$this->getItemMock('simple', null)];

        $this->processorPoolMock->expects($this->once())
            ->method('getProcessorByCode')
            ->with('simple')
            ->willThrowException(
                new ConfigurationMismatchException(
                    __('Item processor must implements %1', ItemProcessorInterface::class)
                )
            );

        $this->expectException(ConfigurationMismatchException::class);

         $this->processor->getEarnItem($items);
    }

    /**
     * Get item mock
     *
     * @param string $productType
     * @param ItemInterface|MockObject|null $parentItem
     * @return ItemInterface|MockObject
     */
    private function getItemMock($productType, $parentItem)
    {
        $simpleItemMock = $this->createMock(ItemInterface::class);
        $simpleItemMock->expects($this->any())
            ->method('getProductType')
            ->willReturn($productType);
        $simpleItemMock->expects($this->any())
            ->method('getParentItem')
            ->willReturn($parentItem);

        return $simpleItemMock;
    }
}
