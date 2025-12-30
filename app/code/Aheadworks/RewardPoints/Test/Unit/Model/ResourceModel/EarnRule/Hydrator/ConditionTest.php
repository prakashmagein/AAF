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
namespace Aheadworks\RewardPoints\Test\Unit\Model\ResourceModel\EarnRule\Hydrator;

use Aheadworks\RewardPoints\Api\Data\ConditionInterface;
use Aheadworks\RewardPoints\Api\Data\EarnRuleInterface;
use Aheadworks\RewardPoints\Model\Condition;
use Aheadworks\RewardPoints\Model\EarnRule\Condition\Converter as ConditionConverter;
use Aheadworks\RewardPoints\Model\ResourceModel\EarnRule\Hydrator\Condition as ConditionHydrator;
use Magento\Framework\Serialize\Serializer\Serialize;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Model\ResourceModel\EarnRule\Hydrator\Condition
 */
class ConditionTest extends TestCase
{
    /**
     * @var ConditionHydrator
     */
    private $hydrator;

    /**
     * @var ConditionConverter|MockObject
     */
    private $conditionConverterMock;

    /**
     * @var Serialize
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
        $this->serializerMock = $this->getMockBuilder(Serialize::class)
            ->setMethods(
                ['serialize']
            )
            ->getMock();

        $this->hydrator = $objectManager->getObject(
            ConditionHydrator::class,
            [
                'conditionConverter' => $this->conditionConverterMock,
                'serializer' => $this->serializerMock
            ]
        );
    }

    /**
     * Test extract method
     */
    public function testExtract()
    {
        $conditionMock = $this->createMock(ConditionInterface::class);
        $conditionData = [
            Condition::AGGREGATOR => 'all'
        ];
        $serializedConditionData = 'a:1:{s:' . strlen(Condition::AGGREGATOR) . ':"'
            . Condition::AGGREGATOR . '";s:3:"all";}';
        $this->serializerMock
            ->method('serialize')
            ->with($conditionData)
            ->willReturn($serializedConditionData);
        $result = [
            EarnRuleInterface::CONDITION => $serializedConditionData
        ];

        $ruleMock = $this->createMock(EarnRuleInterface::class);
        $ruleMock->expects($this->once())
            ->method('getCondition')
            ->willReturn($conditionMock);

        $this->conditionConverterMock->expects($this->once())
            ->method('dataModelToArray')
            ->with($conditionMock)
            ->willReturn($conditionData);

        $this->assertEquals($result, $this->hydrator->extract($ruleMock));
    }

    /**
     * Test extract method if no condition
     */
    public function testExtractNoCondition()
    {
        $ruleMock = $this->createMock(EarnRuleInterface::class);
        $ruleMock->expects($this->once())
            ->method('getCondition')
            ->willReturn(null);

        $this->assertEquals([], $this->hydrator->extract($ruleMock));
    }

    /**
     * Test hydrate method
     */
    public function testHydrate()
    {
        $conditionData = [
            Condition::AGGREGATOR => 'all'
        ];
        $serializedConditionData = 'a:1:{s:' . strlen(Condition::AGGREGATOR) . ':"'
            . Condition::AGGREGATOR . '";s:3:"all";}';
        $data = [
            EarnRuleInterface::CONDITION => $serializedConditionData
        ];
        $conditionMock = $this->createMock(ConditionInterface::class);

        $this->conditionConverterMock->expects($this->once())
            ->method('arrayToDataModel')
            ->with($conditionData)
            ->willReturn($conditionMock);

        $ruleMock = $this->createMock(EarnRuleInterface::class);
        $ruleMock->expects($this->once())
            ->method('setCondition')
            ->with($conditionMock)
            ->willReturnSelf();

        $this->assertEquals($ruleMock, $this->hydrator->hydrate($ruleMock, $data));
    }

    /**
     * Test hydrate method if no condition data
     */
    public function testHydrateNoCondition()
    {
        $data = [];

        $ruleMock = $this->createMock(EarnRuleInterface::class);
        $ruleMock->expects($this->never())
            ->method('setCondition');

        $this->assertEquals($ruleMock, $this->hydrator->hydrate($ruleMock, $data));
    }
}
