<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Test\Integration\Model\SelectResolver;

use Amasty\ReportBuilder\Api\Data\ReportColumnInterface;
use Amasty\ReportBuilder\Api\Data\SelectColumnInterface;
use Amasty\ReportBuilder\Model\EntityScheme\Column\AggregationType;
use Amasty\ReportBuilder\Model\Report\ColumnRegistry;
use Amasty\ReportBuilder\Model\ReportRegistry;
use Amasty\ReportBuilder\Model\ReportResolver;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\ColumnResolver;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\ColumnStorageInterface;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\SelectEavColumn;
use Amasty\ReportBuilder\Test\Registry;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea adminhtml
 * @magentoAppIsolation disabled
 * @magentoDbIsolation disabled
 * @magentoDataFixture Amasty_ReportBuilder::Test/_files/report.php
 */
class ColumnResolverTest extends TestCase
{
    /**
     * @var ColumnResolver
     */
    private $model;

    /**
     * @var ColumnRegistry
     */
    private $columnRegistry;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var ReportResolver $reportResolver */
        $this->objectManager = Bootstrap::getObjectManager();
        $reportResolver = $this->objectManager->get(ReportResolver::class);
        $reportResolver->resolve(Registry::$REPORT_ID);
        $this->model = $this->objectManager->get(ColumnResolver::class);
        $this->columnRegistry = $this->objectManager->get(ColumnRegistry::class);
        $this->columnRegistry->clear();
    }

    /**
     * Clear registry.
     * This method is called after each test.
     */
    protected function tearDown(): void
    {
        Bootstrap::getObjectManager()->get(ReportRegistry::class)->clear();
        Bootstrap::getObjectManager()->get(ColumnStorageInterface::class)->clear();
        $this->columnRegistry->clear();
    }

    /**
     * @dataProvider dataProvider
     */
    public function testExecute(array $reportColumns, array $expectedResult): void
    {
        foreach ($reportColumns as $columnData) {
            $column = $this->objectManager->create(ReportColumnInterface::class, ['data' => $columnData]);
            $this->columnRegistry->addItem(Registry::$REPORT_ID, $column);
        }

        $result = $this->model->resolve();
        self::assertEquals($expectedResult, $result->toArray());
    }

    /**
     * @return int
     */
    protected function getPriceId(): int
    {
        return (int) Bootstrap::getObjectManager()->get(Attribute::class)
            ->getIdByCode('catalog_product', 'price');
    }

    /**
     * @return array[][]
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function dataProvider(): array
    {
        return [
            'simple columns' => [
                [
                    'order.entity_id' => [
                        ReportColumnInterface::COLUMN_ID => 'order.entity_id',
                        ReportColumnInterface::AGGREGATION_TYPE => AggregationType::TYPE_COUNT,
                        ReportColumnInterface::FILTER => '{"from":500,"to":""}',
                        ReportColumnInterface::VISIBILITY => true,
                        ReportColumnInterface::POSITION => 1,
                        ReportColumnInterface::CUSTOM_TITLE => 'Custom title',
                    ],
                    'order.grand_total' => [
                        ReportColumnInterface::COLUMN_ID => 'order.grand_total',
                        ReportColumnInterface::AGGREGATION_TYPE => AggregationType::TYPE_SUM,
                    ],
                    'order_item.sku' => [
                        ReportColumnInterface::COLUMN_ID => 'order_item.sku'
                    ],
                    'NOT_EXIST' => [
                        ReportColumnInterface::COLUMN_ID => 'NOT_EXIST'
                    ],
                ],
                [
                    'order.entity_id' => [
                        SelectColumnInterface::ALIAS => 'order_entity_id',
                        SelectColumnInterface::EXPRESSION => 'order.entity_id',
                        SelectColumnInterface::ENTITY_NAME => 'order',
                        SelectColumnInterface::AGGREGATED_EXPRESSION => 'COUNT(DISTINCT %s)',
                        SelectColumnInterface::USE_AGGREGATION => false,
                        SelectColumnInterface::COLUMN_ID => 'order.entity_id',
                        SelectColumnInterface::EXTERNAL_AGGREGATED_EXPRESSION => 'SUM(%s)'
                    ],
                    'order.grand_total' => [
                        SelectColumnInterface::ALIAS => 'order_grand_total',
                        SelectColumnInterface::EXPRESSION => 'order.grand_total',
                        SelectColumnInterface::ENTITY_NAME => 'order',
                        SelectColumnInterface::AGGREGATED_EXPRESSION => 'SUM(%s)',
                        SelectColumnInterface::USE_AGGREGATION => false,
                        SelectColumnInterface::COLUMN_ID => 'order.grand_total',
                        SelectColumnInterface::EXTERNAL_AGGREGATED_EXPRESSION => 'SUM(%s)'
                    ],
                    'order_item.sku' => [
                        SelectColumnInterface::ALIAS => 'order_item_sku',
                        SelectColumnInterface::EXPRESSION => 'order_item_sku',
                        SelectColumnInterface::ENTITY_NAME => 'order_item',
                        SelectColumnInterface::AGGREGATED_EXPRESSION =>
                            'GROUP_CONCAT(DISTINCT IF(%1$s = "", NULL, %1$s) separator ",")',
                        SelectColumnInterface::USE_AGGREGATION => true,
                        SelectColumnInterface::COLUMN_ID => 'order_item.sku',
                        SelectColumnInterface::EXPRESSION_INTERNAL => 'order_item.sku',
                        SelectColumnInterface::EXTERNAL_AGGREGATED_EXPRESSION =>
                            'GROUP_CONCAT(DISTINCT IF(%1$s = "", NULL, %1$s) separator ",")'
                    ]
                ]
            ],
            'with eav' => [
                [
                    'order.grand_total' => [
                        ReportColumnInterface::COLUMN_ID => 'order.grand_total',
                        ReportColumnInterface::AGGREGATION_TYPE => AggregationType::TYPE_SUM,
                    ],
                    'catalog_product.price' => [
                        ReportColumnInterface::COLUMN_ID => 'catalog_product.price'
                    ],
                ],
                [
                    'order.grand_total' => [
                        SelectColumnInterface::ALIAS => 'order_grand_total',
                        SelectColumnInterface::EXPRESSION => 'order.grand_total',
                        SelectColumnInterface::ENTITY_NAME => 'order',
                        SelectColumnInterface::AGGREGATED_EXPRESSION => 'SUM(%s)',
                        SelectColumnInterface::USE_AGGREGATION => false,
                        SelectColumnInterface::COLUMN_ID => 'order.grand_total',
                        SelectColumnInterface::EXTERNAL_AGGREGATED_EXPRESSION => 'SUM(%s)'
                    ],
                    'catalog_product.price' => [
                        SelectColumnInterface::ALIAS => 'catalog_product_price',
                        SelectColumnInterface::EXPRESSION => 'catalog_product_price',
                        SelectColumnInterface::ENTITY_NAME => 'catalog_product',
                        SelectColumnInterface::AGGREGATED_EXPRESSION => 'SUM(%s)',
                        SelectColumnInterface::USE_AGGREGATION => true,
                        SelectColumnInterface::COLUMN_ID => 'catalog_product.price',
                        SelectEavColumn::ATTRIBUTE_ID => $this->getPriceId(),
                        SelectColumnInterface::EXPRESSION_INTERNAL => 'catalog_product.price',
                        SelectColumnInterface::EXTERNAL_AGGREGATED_EXPRESSION => 'SUM(%s)'
                    ],
                ]
            ]
        ];
    }
}
