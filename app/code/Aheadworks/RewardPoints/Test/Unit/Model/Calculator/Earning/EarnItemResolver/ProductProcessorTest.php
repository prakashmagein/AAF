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
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ProductProcessor;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ProductProcessor\TypeProcessorInterface;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ProductProcessor\TypeProcessorPool;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\ConfigurationMismatchException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ProductProcessor
 */
class ProductProcessorTest extends TestCase
{
    /**
     * @var ProductProcessor
     */
    private $processor;

    /**
     * @var TypeProcessorPool|MockObject
     */
    private $typeProcessorPoolMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->typeProcessorPoolMock = $this->createMock(TypeProcessorPool::class);

        $this->processor = $objectManager->getObject(
            ProductProcessor::class,
            [
                'typeProcessorPool' => $this->typeProcessorPoolMock,
            ]
        );
    }

    /**
     * Test getEarnItems method
     *
     * @param bool $beforeTax
     * @dataProvider getEarnItemsDataProvider
     * @throws \Exception
     */
    public function testGetEarnItems($beforeTax)
    {
        $productType = 'simple';

        $productMock = $this->createMock(Product::class);
        $productMock->expects($this->once())
            ->method('getTypeId')
            ->willReturn($productType);

        $earnItemMock = $this->createMock(EarnItemInterface::class);
        $earnItems = [$earnItemMock];

        $typeProcessorMock = $this->createMock(TypeProcessorInterface::class);
        $this->typeProcessorPoolMock->expects($this->once())
            ->method('getProcessorByCode')
            ->with($productType)
            ->willReturn($typeProcessorMock);

        $typeProcessorMock->expects($this->once())
            ->method('getEarnItems')
            ->with($productMock, $beforeTax)
            ->willReturn($earnItems);

        $this->assertEquals($earnItems, $this->processor->getEarnItems($productMock, $beforeTax));
    }

    /**
     * @return array
     */
    public function getEarnItemsDataProvider()
    {
        return [
            ['beforeTax' => true],
            ['beforeTax' => false],
        ];
    }

    /**
     * Test getEarnItems method if an exception occurs
     */
    public function testGetEarnItemsException()
    {
        $productType = 'simple';

        $productMock = $this->createMock(Product::class);
        $productMock->expects($this->once())
            ->method('getTypeId')
            ->willReturn($productType);

        $this->typeProcessorPoolMock->expects($this->once())
            ->method('getProcessorByCode')
            ->with($productType)
            ->willThrowException(
                new ConfigurationMismatchException(
                    __('Type processor must implements %1', TypeProcessorInterface::class)
                )
            );

        $this->expectException(ConfigurationMismatchException::class);

        $this->processor->getEarnItems($productMock, true);
    }
}
