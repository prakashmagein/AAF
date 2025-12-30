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
namespace Aheadworks\RewardPoints\Test\Unit\Model\ResourceModel\EarnRule\Indexer\Product\DataCollector;

use Aheadworks\RewardPoints\Api\Data\EarnRuleInterface;
use Aheadworks\RewardPoints\Model\EarnRule\ProductMatcher;
use Aheadworks\RewardPoints\Model\EarnRule\ProductMatcher\Result as ProductMatcherResult;
use Aheadworks\RewardPoints\Model\EarnRule\ProductMatcher\Result\Item as ProductMatcherResultItem;
use Aheadworks\RewardPoints\Model\ResourceModel\EarnRule\Indexer\Product\DataCollector\ProductMatcherItemsProcessor;
use Aheadworks\RewardPoints\Model\ResourceModel\EarnRule\Indexer\Product\DataCollector\RuleProcessor;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Model\ResourceModel\EarnRule\Indexer\Product\DataCollector\RuleProcessor
 */
class RuleProcessorTest extends TestCase
{
    /**
     * @var RuleProcessor
     */
    private $processor;

    /**
     * @var ProductMatcher|MockObject
     */
    private $productMatcherMock;

    /**
     * @var ProductMatcherItemsProcessor|MockObject
     */
    private $productMatcherItemsProcessorMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->productMatcherMock = $this->createMock(ProductMatcher::class);
        $this->productMatcherItemsProcessorMock = $this->createMock(ProductMatcherItemsProcessor::class);

        $this->processor = $objectManager->getObject(
            RuleProcessor::class,
            [
                'productMatcher' => $this->productMatcherMock,
                'productMatcherItemsProcessor' => $this->productMatcherItemsProcessorMock
            ]
        );
    }

    /**
     * Test getRuleAllProductsData method
     */
    public function testGetRuleAllProductsData()
    {
        $resultItemMock = $this->createMock(ProductMatcherResultItem::class);
        $items = [$resultItemMock];
        $resultItemsData = ['processed-data'];

        $ruleMock = $this->createMock(EarnRuleInterface::class);
        $resultMock = $this->createMock(ProductMatcherResult::class);
        $resultMock->expects($this->once())
            ->method('getTotalCount')
            ->willReturn(count($items));
        $resultMock->expects($this->once())
            ->method('getItems')
            ->willReturn($items);

        $this->productMatcherMock->expects($this->once())
            ->method('matchAllProducts')
            ->with($ruleMock)
            ->willReturn($resultMock);

        $this->productMatcherItemsProcessorMock->expects($this->once())
            ->method('prepareData')
            ->with($items, $ruleMock)
            ->willReturn($resultItemsData);

        $this->assertEquals($resultItemsData, $this->processor->getAllMatchingProductsData($ruleMock));
    }

    /**
     * Test getRuleAllProductsData method if no product matches the rule
     */
    public function testGetRuleAllProductsDataNoProducts()
    {
        $items = [];
        $resultItemsData = [];

        $ruleMock = $this->createMock(EarnRuleInterface::class);
        $resultMock = $this->createMock(ProductMatcherResult::class);
        $resultMock->expects($this->once())
            ->method('getTotalCount')
            ->willReturn(count($items));
        $resultMock->expects($this->never())
            ->method('getItems');

        $this->productMatcherMock->expects($this->once())
            ->method('matchAllProducts')
            ->with($ruleMock)
            ->willReturn($resultMock);

        $this->productMatcherItemsProcessorMock->expects($this->never())
            ->method('prepareData');

        $this->assertEquals($resultItemsData, $this->processor->getAllMatchingProductsData($ruleMock));
    }

    /**
     * Test getRuleProductData method
     */
    public function testGetRuleProductData()
    {
        $resultItemMock = $this->createMock(ProductMatcherResultItem::class);
        $items = [$resultItemMock];
        $resultItemsData = ['processed-data'];

        $productMock = $this->createMock(ProductInterface::class);
        $ruleMock = $this->createMock(EarnRuleInterface::class);

        $resultMock = $this->createMock(ProductMatcherResult::class);
        $resultMock->expects($this->once())
            ->method('getTotalCount')
            ->willReturn(count($items));
        $resultMock->expects($this->once())
            ->method('getItems')
            ->willReturn($items);

        $this->productMatcherMock->expects($this->once())
            ->method('matchProduct')
            ->with($productMock, $ruleMock)
            ->willReturn($resultMock);

        $this->productMatcherItemsProcessorMock->expects($this->once())
            ->method('prepareData')
            ->with($items, $ruleMock)
            ->willReturn($resultItemsData);

        $this->assertEquals($resultItemsData, $this->processor->getMatchingProductData($ruleMock, $productMock));
    }

    /**
     * Test getRuleProductData method if the product does not match the rule
     */
    public function testGetRuleProductDataNotMatch()
    {
        $items = [];
        $resultItemsData = [];

        $productMock = $this->createMock(ProductInterface::class);
        $ruleMock = $this->createMock(EarnRuleInterface::class);

        $resultMock = $this->createMock(ProductMatcherResult::class);
        $resultMock->expects($this->once())
            ->method('getTotalCount')
            ->willReturn(count($items));
        $resultMock->expects($this->never())
            ->method('getItems');

        $this->productMatcherMock->expects($this->once())
            ->method('matchProduct')
            ->with($productMock, $ruleMock)
            ->willReturn($resultMock);

        $this->productMatcherItemsProcessorMock->expects($this->never())
            ->method('prepareData');

        $this->assertEquals($resultItemsData, $this->processor->getMatchingProductData($ruleMock, $productMock));
    }
}
