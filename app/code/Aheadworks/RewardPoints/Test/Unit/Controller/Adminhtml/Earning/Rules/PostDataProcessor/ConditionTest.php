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
namespace Aheadworks\RewardPoints\Test\Unit\Controller\Adminhtml\Earning\Rules\PostDataProcessor;

use Aheadworks\RewardPoints\Api\Data\ConditionInterface;
use Aheadworks\RewardPoints\Controller\Adminhtml\Earning\Rules\PostDataProcessor\Condition;
use Aheadworks\RewardPoints\Model\EarnRule\Condition\Cart\Combine;
use Aheadworks\RewardPoints\Model\EarnRule\Condition\Converter as ConditionConverter;
use Aheadworks\RewardPoints\Model\EarnRule\Condition\Rule as ConditionRule;
use Aheadworks\RewardPoints\Model\EarnRule\Condition\Rule\CartFactory;
use Aheadworks\RewardPoints\Model\Source\EarnRule\Type;
use Magento\CatalogRule\Model\Rule\Condition\Product as ProductCondition;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\SalesRule\Model\Rule\Condition\Product\Found;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Controller\Adminhtml\Earning\Rules\PostDataProcessor\Condition
 */
class ConditionTest extends TestCase
{
    /**
     * @var Condition
     */
    private $processor;

    /**
     * @var ConditionConverter|MockObject
     */
    private $conditionConverterMock;

    /**
     * @var CartFactory|MockObject
     */
    private $cartRuleFactoryMock;

    /**
     * @var array
     */
    private $typeConditionMapMock;

    /**
     * @var Json|MockObject
     */
    private $serializerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->conditionConverterMock = $this->createMock(ConditionConverter::class);
        $this->cartRuleFactoryMock = $this->createMock(CartFactory::class);
        $this->typeConditionMapMock = ['catalog' => 'catalog', 'cart' => 'conditions'];
        $this->serializerMock = $this->createMock(Json::class);

        $this->processor = $objectManager->getObject(
            Condition::class,
            [
                'conditionConverter' => $this->conditionConverterMock,
                'cartRuleFactory' => $this->cartRuleFactoryMock,
                'serializer' => $this->serializerMock,
                'typeConditionMap' => $this->typeConditionMapMock
            ]
        );
    }

    /**
     * Test process method
     *
     * @param array $data
     * @param array $convertedData
     * @param ConditionInterface|MockObject $dataModel
     * @param array $resaltDataModelToArray
     * @param array $result
     * @dataProvider processDataProvider
     */
    public function testProcess($data, $convertedData, $dataModel, $resaltDataModelToArray, $result)
    {
        $this->conditionConverterMock->expects($this->once())
            ->method('arrayToDataModel')
            ->with($convertedData)
            ->willReturn($dataModel);

        $this->conditionConverterMock->expects($this->once())
            ->method('dataModelToArray')
            ->with($dataModel)
            ->willReturn($resaltDataModelToArray);

        $this->assertEquals($result, $this->processor->process($data));
    }

    /**
     * @return array
     */
    public function processDataProvider()
    {
        $dataModel = $this->createMock(ConditionInterface::class);
        return [
            [
                'data' => [
                    'type' => 'cart',
                    'condition' => [
                        'type' => Combine::class,
                        'conditions' => [
                            '0' => [
                                'type' => Found::class,
                                'conditions' => [
                                    '0' => [
                                        'type' => ProductCondition::class,
                                        'operator' => '==',
                                        'attribute' => 'category_ids',
                                        'value' => '6',
                                    ]
                                ],
                                'aggregator' => 'all',
                                'value' => '1',
                            ],
                        ],
                        'aggregator' => 'all',
                        'value' => '1',
                    ],
                    'rule' => [
                        Type::CATALOG => [
                            '1' => [
                                'type' => Combine::class,
                                'aggregator' => 'all',
                                'value' => '1',
                                'new_child' => '',
                            ]
                        ],
                        ConditionRule::CONDITIONS_PREFIX => [
                            '1' => [
                                'type' => Combine::class,
                                'aggregator' => 'all',
                                'value' => '1',
                                'new_child' => '',
                            ],
                            '1--1' => [
                                'type' => Found::class,
                                'value' => '1',
                                'aggregator' => 'all',
                                'new_child' => '',
                            ],
                            '1--1--1' => [
                                'type' => ProductCondition::class,
                                'attribute' => 'category_ids',
                                'attribute_scope' => '',
                                'operator' => '==',
                                'value' => '6',
                            ],
                        ]
                    ]
                ],
                'convertedData' => [
                    'type' => Combine::class,
                    'aggregator' => 'all',
                    'value' => '1',
                    'new_child' => '',
                    'conditions' => [
                        '1' => [
                            'type' => Found::class,
                            'value' => '1',
                            'aggregator' => 'all',
                            'new_child' => '',
                            'conditions' => [
                                '1' => [
                                    'type' => ProductCondition::class,
                                    'attribute' => 'category_ids',
                                    'attribute_scope' => '',
                                    'operator' => '==',
                                    'value' => '6',
                                ]
                            ]
                        ]
                    ]
                ],
                'dataModel ' => $dataModel,
                'dataModelToArray' => [
                    'type' => Combine::class,
                    'attribute' => 'null',
                    'operator' => 'null',
                    'value' => '1',
                    'value_type' => 'null',
                    'aggregator' => 'all',
                    'conditions' => [
                        '0' => [
                            'type' => Found::class,
                            'attribute' => 'null',
                            'operator' => 'null',
                            'value' => '1',
                            'value_type' => 'null',
                            'aggregator' => 'all',
                            'conditions' => [
                                '0' => [
                                    'type' => ProductCondition::class,
                                    'attribute' => 'category_ids',
                                    'operator' => '==',
                                    'value' => '6',
                                    'value_type' => 'null',
                                    'aggregator' => 'null',
                                ]
                            ]
                        ]
                    ]
                ],
                'result' => [
                    'type' => 'cart',
                    'condition' => [
                        'type' => Combine::class,
                        'attribute' => 'null',
                        'operator' => 'null',
                        'value' => '1',
                        'value_type' => 'null',
                        'aggregator' => 'all',
                        'conditions' => [
                            '0' => [
                                'type' => Found::class,
                                'attribute' => 'null',
                                'operator' => 'null',
                                'value' => '1',
                                'value_type' => 'null',
                                'aggregator' => 'all',
                                'conditions' => [
                                    '0' => [
                                        'type' => ProductCondition::class,
                                        'attribute' => 'category_ids',
                                        'operator' => '==',
                                        'value' => '6',
                                        'value_type' => 'null',
                                        'aggregator' => 'null',
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'rule' => [
                        Type::CATALOG => [
                            '1' => [
                                'type' => Combine::class,
                                'aggregator' => 'all',
                                'value' => '1',
                                'new_child' => '',
                            ]
                        ],
                        ConditionRule::CONDITIONS_PREFIX => [
                            '1' => [
                                'type' => Combine::class,
                                'aggregator' => 'all',
                                'value' => '1',
                                'new_child' => '',
                            ],
                            '1--1' => [
                                'type' => Found::class,
                                'value' => '1',
                                'aggregator' => 'all',
                                'new_child' => '',
                            ],
                            '1--1--1' => [
                                'type' => ProductCondition::class,
                                'attribute' => 'category_ids',
                                'attribute_scope' => '',
                                'operator' => '==',
                                'value' => '6',
                            ],
                        ]
                    ]
                ]
            ]
        ];
    }
}
