<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Test\Integration\Model\Backend\Report;

use Amasty\ReportBuilder\Api\ColumnInterface;
use Amasty\ReportBuilder\Api\Data\ReportColumnInterface;
use Amasty\ReportBuilder\Api\Data\ReportInterface;
use Amasty\ReportBuilder\Api\Data\ReportInterfaceFactory;
use Amasty\ReportBuilder\Model\Backend\Report\DataCollector;
use Amasty\ReportBuilder\Model\Backend\Report\DataCollector\Columns;
use Amasty\ReportBuilder\Model\Backend\Report\DataCollector\Columns\FilterCollector;
use Amasty\ReportBuilder\Model\EntityScheme\Column\AggregationType;
use Amasty\ReportBuilder\Model\Report\ColumnRegistry;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea adminhtml
 * @magentoAppIsolation disabled
 * @magentoDbIsolation disabled
 */
class DataCollectorTest extends TestCase
{
    /**
     * @var DataCollector
     */
    private $model;

    /**
     * @var ReportInterfaceFactory
     */
    private $reportFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reportFactory = Bootstrap::getObjectManager()->get(ReportInterfaceFactory::class);
        $this->model = Bootstrap::getObjectManager()->get(DataCollector::class);
    }

    /**
     * Clear items' registry.
     * This method is called after each test.
     */
    protected function tearDown(): void
    {
        Bootstrap::getObjectManager()->get(ColumnRegistry::class)->clear();
    }

    /**
     * @dataProvider dataProvider
     */
    public function testExecute(array $inputData, array $expectedResult): void
    {
        $report = $this->reportFactory->create();
        $this->model->execute($report, $inputData);
        self::assertEquals($expectedResult, $report->getData());
    }

    /**
     * @return array[][]
     * @throws \JsonException
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function dataProvider(): array
    {
        return [
            'simple' => [
                [
                    ReportInterface::MAIN_ENTITY => 'order',
                    ReportInterface::STORE_IDS => 0,
                    ReportInterface::NAME => 'Test',
                    ReportInterface::DISPLAY_CHART => false,
                    ReportInterface::CHART_AXIS_X => '',
                    ReportInterface::CHART_AXIS_Y => '',
                    Columns::COLUMNS_DATA_KEY =>
                        json_encode([
                            [
                                Columns::COLUMN_DATA_ID => 'order.entity_id',
                                Columns::COLUMN_DATA_VISIBILITY => 1,
                                Columns::COLUMN_DATA_POSITION => 1,
                                Columns::COLUMN_DATA_CUSTOM_TITLE => 'Custom title',
                                Columns::COLUMN_DATA_AGGREGATION => [
                                    Columns::ACTIVE_KEY => true,
                                    Columns::VALUE_KEY => AggregationType::TYPE_COUNT
                                ],
                                FilterCollector::COLUMN_DATA_FILTER => [
                                    FilterCollector::COLUMN_DATA_FILTER_IS_ACTIVE => true,
                                    FilterCollector::COLUMN_DATA_FILTER_VALUE => ['from' => 500, 'to' => '']
                                ],
                                ColumnInterface::TYPE => 'default'
                            ]
                        ], JSON_THROW_ON_ERROR)
                ],
                [
                    ReportInterface::REPORT_ID => null,
                    ReportInterface::USE_PERIOD => false,
                    ReportInterface::MAIN_ENTITY => 'order',
                    ReportInterface::STORE_IDS => 0,
                    ReportInterface::NAME => 'Test',
                    ReportInterface::DISPLAY_CHART => false,
                    ReportInterface::CHART_AXIS_X => '',
                    ReportInterface::CHART_AXIS_Y => '',
                    ReportInterface::COLUMNS => [
                        'order.entity_id' => [
                            ReportColumnInterface::COLUMN_ID => 'order.entity_id',
                            ReportColumnInterface::AGGREGATION_TYPE => AggregationType::TYPE_COUNT,
                            ReportColumnInterface::FILTER => '{"from":500,"to":""}',
                            ReportColumnInterface::VISIBILITY => true,
                            ReportColumnInterface::POSITION => 1,
                            ReportColumnInterface::CUSTOM_TITLE => 'Custom title',
                        ]
                    ],
                    ReportInterface::SCHEME => [

                    ]
                ]
            ],
            'with dependency' => [
                [
                    ReportInterface::MAIN_ENTITY => 'order',
                    ReportInterface::NAME => 'Test',
                    Columns::COLUMNS_DATA_KEY =>
                        json_encode([
                            [
                                Columns::COLUMN_DATA_ID => 'order.entity_id',
                                ColumnInterface::TYPE => 'default'
                            ],
                            [
                                Columns::COLUMN_DATA_ID => 'order_item.sku',
                                ColumnInterface::TYPE => 'default'
                            ],
                            [
                                Columns::COLUMN_DATA_ID => 'order_item.product_type',
                                ColumnInterface::TYPE => 'default'
                            ]
                        ], JSON_THROW_ON_ERROR)
                ],
                [
                    ReportInterface::REPORT_ID => null,
                    ReportInterface::USE_PERIOD => false,
                    ReportInterface::MAIN_ENTITY => 'order',
                    ReportInterface::STORE_IDS => [0],
                    ReportInterface::NAME => 'Test',
                    ReportInterface::DISPLAY_CHART => false,
                    ReportInterface::CHART_AXIS_X => '',
                    ReportInterface::CHART_AXIS_Y => '',
                    ReportInterface::COLUMNS => [
                        'order.entity_id' => [
                            ReportColumnInterface::COLUMN_ID => 'order.entity_id',
                            ReportColumnInterface::AGGREGATION_TYPE => null,
                            ReportColumnInterface::FILTER => ''
                        ],
                        'order_item.sku' => [
                            ReportColumnInterface::COLUMN_ID => 'order_item.sku',
                            ReportColumnInterface::AGGREGATION_TYPE => null,
                            ReportColumnInterface::FILTER => '',
                        ],
                        'order_item.product_type' => [
                            ReportColumnInterface::COLUMN_ID => 'order_item.product_type',
                            ReportColumnInterface::AGGREGATION_TYPE => null,
                            ReportColumnInterface::FILTER => '',
                        ],
                    ],
                    ReportInterface::SCHEME => [
                        [
                            ReportInterface::SCHEME_SOURCE_ENTITY => 'order',
                            ReportInterface::SCHEME_ENTITY => 'order_item'
                        ]
                    ]
                ]
            ],
        ];
    }
}
