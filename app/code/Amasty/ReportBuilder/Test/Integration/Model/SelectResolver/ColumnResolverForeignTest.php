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
use Amasty\ReportBuilder\Api\EntityInterface;
use Amasty\ReportBuilder\Model\EntityScheme;
use Amasty\ReportBuilder\Model\EntityScheme\Provider;
use Amasty\ReportBuilder\Model\Report\ColumnRegistry;
use Amasty\ReportBuilder\Model\ReportRegistry;
use Amasty\ReportBuilder\Model\ReportResolver;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\ColumnResolver;
use Amasty\ReportBuilder\Model\SelectResolver\SelectColumn\ColumnStorageInterface;
use Amasty\ReportBuilder\Test\Registry;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea adminhtml
 * @magentoAppIsolation disabled
 * @magentoDbIsolation disabled
 * @magentoDataFixture Amasty_ReportBuilder::Test/_files/report.php
 * @magentoDataFixture Amasty_ReportBuilder::Test/_files/foreign_table.php
 */
class ColumnResolverForeignTest extends TestCase
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

        $this->addForeignSchema();
        $this->addSchemaColumns();
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
     * @return array[][]
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function dataProvider(): array
    {
        return [
            'simple test' => [
                [
                    'order_item.test' => [
                        ReportColumnInterface::COLUMN_ID => 'order_item.test'
                    ]
                ],
                [
                    'order_item.test' => [
                        SelectColumnInterface::ALIAS => 'order_item_test',
                        SelectColumnInterface::EXPRESSION => 'order_item_test',
                        SelectColumnInterface::ENTITY_NAME => 'order_item_foreign',
                        SelectColumnInterface::AGGREGATED_EXPRESSION => 'IF(MAX(%s) > 0, 1, 0)',
                        SelectColumnInterface::USE_AGGREGATION => true,
                        SelectColumnInterface::COLUMN_ID => 'order_item.test',
                        SelectColumnInterface::EXPRESSION_INTERNAL => 'order_item_foreign.is_foreign',
                        SelectColumnInterface::EXTERNAL_AGGREGATED_EXPRESSION => 'IF(MAX(%s) > 0, 1, 0)'
                    ],
                ]
            ]
        ];
    }

    public function addForeignSchema(): void
    {
        $schemaData = [
            EntityInterface::NAME => 'order_item_foreign',
            EntityInterface::TITLE => 'Order Item Foreign Entity',
            EntityInterface::MAIN_TABLE => 'amasty_test_order_item_foreign',
            EntityInterface::HIDDEN => true,
            EntityInterface::COLUMNS =>
                [
                    'is_foreign' =>
                        [
                            'name' => 'is_foreign',
                            'title' => 'Is_foreign',
                            'type' => 'boolean',
                            'column_type' => 'default',
                            'source_model' => Yesno::class,
                            'use_for_period' => false,
                            'frontend_model' => '',
                            'primary' => false,
                            'aggregation_type' => 'max',
                            'custom_expression' => 'zero_or_one',
                        ],
                    'order_item_id' =>
                        [
                            'name' => 'order_item_id',
                            'title' => 'Order_item_id',
                            'type' => 'int',
                            'column_type' => 'default',
                        ]
                ],
            EntityInterface::RELATIONS =>
                [
                    'order_item' =>
                        [
                            'name' => 'order_item',
                            'type' => 'column',
                            'column' => 'order_item_id',
                            'reference_column' => 'item_id',
                            'relationship_type' => 'one_to_one',
                        ],
                ],
            EntityInterface::PRIMARY => false,
        ];

        /** @var EntityScheme $entityScheme */
        $entityScheme = Bootstrap::getObjectManager()->get(Provider::class)->getEntityScheme();
        $entityScheme->addEntity('order_item_foreign', $schemaData);
    }

    public function addSchemaColumns(): void
    {
        $columns = [
            'test' => [
                'name' => 'test',
                'title' => 'Test Order Item Foreign Column',
                'type' => 'boolean',
                'column_type' => 'foreign',
                'use_for_period' => false,
                'frontend_model' => '',
                'primary' => false,
                'link' => 'order_item_foreign.is_foreign',
                'source_model' => Yesno::class,
                'aggregation_type' => 'max',
                'custom_expression' => 'zero_or_one',
            ]
        ];

        $entityScheme = Bootstrap::getObjectManager()->get(Provider::class)->getEntityScheme();
        $orderItemEntity = $entityScheme->getEntityByName('order_item');
        foreach ($columns as $columnName => $columnData) {
            $orderItemEntity->addColumn($columnName, $columnData);
        }
    }
}
