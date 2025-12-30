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
namespace Aheadworks\RewardPoints\Test\Unit\Model\ResourceModel\EarnRule\Indexer\Product;

use Aheadworks\RewardPoints\Api\Data\EarnRuleInterface;
use Aheadworks\RewardPoints\Api\EarnRuleManagementInterface;
use Aheadworks\RewardPoints\Model\Indexer\EarnRule\ProductLoader;
use Aheadworks\RewardPoints\Model\ResourceModel\EarnRule\Indexer\Product\DataCollector;
use Aheadworks\RewardPoints\Model\ResourceModel\EarnRule\Indexer\Product\DataCollector\RuleProcessor;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Model\ResourceModel\EarnRule\Indexer\Product\DataCollector
 */
class DataCollectorTest extends TestCase
{
    /**
     * @var DataCollector
     */
    private $dataCollector;

    /**
     * @var EarnRuleManagementInterface|MockObject
     */
    private $earnRuleManagementMock;

    /**
     * @var RuleProcessor|MockObject
     */
    private $ruleProcessorMock;

    /**
     * @var ProductLoader|MockObject
     */
    private $productLoaderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->earnRuleManagementMock = $this->createMock(EarnRuleManagementInterface::class);
        $this->ruleProcessorMock = $this->createMock(RuleProcessor::class);
        $this->productLoaderMock = $this->createMock(ProductLoader::class);

        $this->dataCollector = $objectManager->getObject(
            DataCollector::class,
            [
                'earnRuleManagement' => $this->earnRuleManagementMock,
                'ruleProcessor' => $this->ruleProcessorMock,
                'productLoader' => $this->productLoaderMock,
            ]
        );
    }

    /**
     * Test getAllData method
     */
    public function testGetAllData()
    {
        $ruleMock = $this->createMock(EarnRuleInterface::class);
        $activeRules = [$ruleMock];
        $matchingProductsData = ['item1', 'item2', 'item3'];

        $this->earnRuleManagementMock->expects($this->once())
            ->method('getActiveRulesForIndexer')
            ->willReturn($activeRules);

        $this->ruleProcessorMock->expects($this->once())
            ->method('getAllMatchingProductsData')
            ->with($ruleMock)
            ->willReturn($matchingProductsData);

        $this->assertEquals($matchingProductsData, $this->dataCollector->getAllData());
    }

    /**
     * Test getAllData method if no active rules
     */
    public function testGetAllDataNoActiveRules()
    {
        $activeRules = [];

        $this->earnRuleManagementMock->expects($this->once())
            ->method('getActiveRulesForIndexer')
            ->willReturn($activeRules);

        $this->ruleProcessorMock->expects($this->never())
            ->method('getAllMatchingProductsData');

        $this->assertEquals([], $this->dataCollector->getAllData());
    }

    /**
     * Test getAllData method if no matching products found
     */
    public function testGetAllDataNoMatchingProducts()
    {
        $ruleMock = $this->createMock(EarnRuleInterface::class);
        $activeRules = [$ruleMock];
        $matchingProductsData = [];

        $this->earnRuleManagementMock->expects($this->once())
            ->method('getActiveRulesForIndexer')
            ->willReturn($activeRules);

        $this->ruleProcessorMock->expects($this->once())
            ->method('getAllMatchingProductsData')
            ->with($ruleMock)
            ->willReturn($matchingProductsData);

        $this->assertEquals($matchingProductsData, $this->dataCollector->getAllData());
    }

    /**
     * Test getDataToUpdate method
     */
    public function testGetDataToUpdate()
    {
        $ruleMock = $this->createMock(EarnRuleInterface::class);
        $activeRules = [$ruleMock];
        $productIds = [1, 2, 3];
        $productMock = $this->createMock(ProductInterface::class);
        $products = [$productMock];
        $matchingProductsData = ['item1'];

        $this->productLoaderMock->expects($this->once())
            ->method('getProducts')
            ->with($productIds)
            ->willReturn($products);

        $this->earnRuleManagementMock->expects($this->once())
            ->method('getActiveRules')
            ->willReturn($activeRules);

        $this->ruleProcessorMock->expects($this->once())
            ->method('getMatchingProductData')
            ->with($ruleMock, $productMock)
            ->willReturn($matchingProductsData);

        $this->assertEquals($matchingProductsData, $this->dataCollector->getDataToUpdate($productIds));
    }

    /**
     * Test getDataToUpdate method if no active rules found
     */
    public function testGetDataToUpdateNoActiveRules()
    {
        $activeRules = [];
        $productIds = [1, 2, 3];
        $productMock = $this->createMock(ProductInterface::class);
        $products = [$productMock];
        $matchingProductsData = [];

        $this->productLoaderMock->expects($this->once())
            ->method('getProducts')
            ->with($productIds)
            ->willReturn($products);

        $this->earnRuleManagementMock->expects($this->once())
            ->method('getActiveRules')
            ->willReturn($activeRules);

        $this->ruleProcessorMock->expects($this->never())
            ->method('getMatchingProductData');

        $this->assertEquals($matchingProductsData, $this->dataCollector->getDataToUpdate($productIds));
    }

    /**
     * Test getDataToUpdate method if no products found
     */
    public function testGetDataToUpdateNoProducts()
    {
        $ruleMock = $this->createMock(EarnRuleInterface::class);
        $activeRules = [$ruleMock];
        $productIds = [1, 2, 3];
        $products = [];
        $matchingProductsData = [];

        $this->productLoaderMock->expects($this->once())
            ->method('getProducts')
            ->with($productIds)
            ->willReturn($products);

        $this->earnRuleManagementMock->expects($this->once())
            ->method('getActiveRules')
            ->willReturn($activeRules);

        $this->ruleProcessorMock->expects($this->never())
            ->method('getMatchingProductData');

        $this->assertEquals($matchingProductsData, $this->dataCollector->getDataToUpdate($productIds));
    }

    /**
     * Test getDataToUpdate method if no matching products found
     */
    public function testGetDataToUpdateNoMatchingProducts()
    {
        $ruleMock = $this->createMock(EarnRuleInterface::class);
        $activeRules = [$ruleMock];
        $productIds = [1, 2, 3];
        $productMock = $this->createMock(ProductInterface::class);
        $products = [$productMock];
        $matchingProductsData = [];

        $this->productLoaderMock->expects($this->once())
            ->method('getProducts')
            ->with($productIds)
            ->willReturn($products);

        $this->earnRuleManagementMock->expects($this->once())
            ->method('getActiveRules')
            ->willReturn($activeRules);

        $this->ruleProcessorMock->expects($this->once())
            ->method('getMatchingProductData')
            ->with($ruleMock, $productMock)
            ->willReturn($matchingProductsData);

        $this->assertEquals($matchingProductsData, $this->dataCollector->getDataToUpdate($productIds));
    }
}
