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
namespace Aheadworks\RewardPoints\Test\Unit\Model\EarnRule\Condition;

use Aheadworks\RewardPoints\Api\Data\ConditionInterface;
use Aheadworks\RewardPoints\Api\Data\ConditionInterfaceFactory;
use Aheadworks\RewardPoints\Model\EarnRule\Condition\Converter;
use Aheadworks\RewardPoints\Model\EarnRule\Condition\Rule as ConditionRule;
use Magento\CatalogRule\Model\Rule\Condition\Combine as CombineCondition;
use Magento\CatalogRule\Model\Rule\Condition\Product as ProductCondition;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Model\EarnRule\Condition\Converter
 */
class ConverterTest extends TestCase
{
    /**
     * @var Converter
     */
    private $converter;

    /**
     * @var ConditionInterfaceFactory|MockObject
     */
    private $conditionFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->conditionFactoryMock = $this->createMock(ConditionInterfaceFactory::class);

        $this->converter = $objectManager->getObject(
            Converter::class,
            [
                'conditionFactory' => $this->conditionFactoryMock,
            ]
        );
    }

    /**
     * Test arrayToDataModel method
     */
    public function testArrayToDataModel()
    {
        $nestedCondition = [
            'type' => ProductCondition::class,
            'attribute' => 'category_ids',
            'attribute_scope' => null,
            'operator' => '==',
            'value' => "11111",
            'value_type' => null,
            'aggregator' => null,
        ];
        $condition = [
            'type' => CombineCondition::class,
            'attribute' => null,
            'attribute_scope' => null,
            'operator' => null,
            'value' => "1",
            'value_type' => null,
            'aggregator' => 'all',
            ConditionRule::CONDITIONS_PREFIX => [
                $nestedCondition
            ]
        ];

        $nestedConditionMock = $this->getConditionMock($nestedCondition);
        $conditionMock = $this->getConditionMock($condition, $nestedConditionMock);

        $this->conditionFactoryMock->expects($this->exactly(2))
            ->method('create')
            ->willReturnOnConsecutiveCalls($conditionMock, $nestedConditionMock);

        $this->assertSame($conditionMock, $this->converter->arrayToDataModel($condition));
    }

    /**
     * Test arrayToDataModel method if no nested condition
     */
    public function testArrayToDataModelNoNestedCondition()
    {
        $condition = [
            'type' => CombineCondition::class,
            'attribute' => null,
            'attribute_scope' => null,
            'operator' => null,
            'value' => "1",
            'value_type' => null,
            'aggregator' => 'all',
        ];

        $conditionMock = $this->getConditionMock($condition);

        $this->conditionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($conditionMock);

        $this->assertSame($conditionMock, $this->converter->arrayToDataModel($condition));
    }

    /**
     * Test dataModelToArray method
     *
     * @param ConditionInterface|MockObject $condition
     * @param array $result
     * @dataProvider dataModelToArrayDataProvider
     */
    public function testDataModelToArray($condition, $result)
    {
        $this->assertSame($result, $this->converter->dataModelToArray($condition));
    }

    /**
     * @return array
     */
    public function dataModelToArrayDataProvider()
    {
        $conditionNoNested = [
            'type' => CombineCondition::class,
            'attribute' => null,
            'attribute_scope' => null,
            'operator' => null,
            'value' => "1",
            'value_type' => null,
            'aggregator' => 'all',
        ];

        $nestedCondition = [
            'type' => ProductCondition::class,
            'attribute' => 'category_ids',
            'attribute_scope' => null,
            'operator' => '==',
            'value' => "11111",
            'value_type' => null,
            'aggregator' => null,
        ];
        $condition = [
            'type' => CombineCondition::class,
            'attribute' => null,
            'attribute_scope' => null,
            'operator' => null,
            'value' => "1",
            'value_type' => null,
            'aggregator' => 'all',
            ConditionRule::CONDITIONS_PREFIX => [
                $nestedCondition
            ]
        ];

        return [
            [
                'condition' => $this->getConditionMock($conditionNoNested),
                'result' => $conditionNoNested
            ],
            [
                'condition' => $this->getConditionMock($condition, $this->getConditionMock($nestedCondition)),
                'result' => $condition
            ],
        ];
    }

    /**
     * Get condition mock
     *
     * @param array $conditionData
     * @param ConditionInterface|MockObject|null $nestedConditionMock
     * @return ConditionInterface|MockObject
     */
    private function getConditionMock($conditionData, $nestedConditionMock = null)
    {
        $conditionMock = $this->createMock(ConditionInterface::class);
        $conditionMock->expects($this->any())
            ->method('getType')
            ->willReturn($conditionData['type']);
        $conditionMock->expects($this->any())
            ->method('setType')
            ->with($conditionData['type'])
            ->willReturnSelf();
        $conditionMock->expects($this->any())
            ->method('getAttribute')
            ->willReturn($conditionData['attribute']);
        $conditionMock->expects($this->any())
            ->method('setAttribute')
            ->with($conditionData['attribute'])
            ->willReturnSelf();
        $conditionMock->expects($this->any())
            ->method('getAttributeScope')
            ->willReturn($conditionData['attribute_scope']);
        $conditionMock->expects($this->any())
            ->method('setAttributeScope')
            ->with($conditionData['attribute_scope'])
            ->willReturnSelf();
        $conditionMock->expects($this->any())
            ->method('getOperator')
            ->willReturn($conditionData['operator']);
        $conditionMock->expects($this->any())
            ->method('setOperator')
            ->with($conditionData['operator'])
            ->willReturnSelf();
        $conditionMock->expects($this->any())
            ->method('getValue')
            ->willReturn($conditionData['value']);
        $conditionMock->expects($this->any())
            ->method('setValue')
            ->with($conditionData['value'])
            ->willReturnSelf();
        $conditionMock->expects($this->any())
            ->method('getValueType')
            ->willReturn($conditionData['value_type']);
        $conditionMock->expects($this->any())
            ->method('setValueType')
            ->with($conditionData['value_type'])
            ->willReturnSelf();
        $conditionMock->expects($this->any())
            ->method('getAggregator')
            ->willReturn($conditionData['aggregator']);
        $conditionMock->expects($this->any())
            ->method('setAggregator')
            ->with($conditionData['aggregator'])
            ->willReturnSelf();
        if ($nestedConditionMock) {
            $conditionMock->expects($this->any())
                ->method('getConditions')
                ->willReturn([$nestedConditionMock]);
            $conditionMock->expects($this->any())
                ->method('setConditions')
                ->with([$nestedConditionMock])
                ->willReturnSelf();
        } else {
            $conditionMock->expects($this->any())
                ->method('getConditions')
                ->willReturn(null);
        }

        return $conditionMock;
    }
}
