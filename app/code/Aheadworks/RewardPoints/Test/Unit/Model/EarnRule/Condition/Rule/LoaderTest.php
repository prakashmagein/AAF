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
namespace Aheadworks\RewardPoints\Test\Unit\Model\EarnRule\Condition\Rule;

use Aheadworks\RewardPoints\Api\Data\ConditionInterface;
use Aheadworks\RewardPoints\Model\EarnRule\Condition\Converter as ConditionConverter;
use Aheadworks\RewardPoints\Model\EarnRule\Condition\Rule as ConditionRule;
use Aheadworks\RewardPoints\Model\EarnRule\Condition\Rule\Loader as ConditionLoader;
use Aheadworks\RewardPoints\Model\EarnRule\Condition\RuleFactory as ConditionRuleFactory;
use Magento\CatalogRule\Model\Rule\Condition\Combine as CombineCondition;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Model\EarnRule\Condition\Rule\Loader
 */
class LoaderTest extends TestCase
{
    /**
     * @var ConditionLoader
     */
    private $conditionLoader;

    /**
     * @var ConditionRuleFactory|MockObject
     */
    private $conditionRuleFactoryMock;

    /**
     * @var ConditionConverter|MockObject
     */
    private $conditionConverterMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->conditionConverterMock = $this->createMock(ConditionConverter::class);
        $this->conditionRuleFactoryMock = $this->createMock(ConditionRuleFactory::class);

        $this->conditionLoader = $objectManager->getObject(
            ConditionLoader::class,
            [
                'conditionConverter' => $this->conditionConverterMock,
                'conditionRuleFactory' => $this->conditionRuleFactoryMock,
            ]
        );
    }

    /**
     * Test loadRule method
     *
     * @param ConditionInterface|MockObject $condition
     * @dataProvider loadRuleDataProvider
     */
    public function testLoadRule($condition)
    {
        $conditionData = [
            'aggregator' => 'all'
        ];

        $conditionRuleMock = $this->createMock(ConditionRule::class);
        $this->conditionRuleFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($conditionRuleMock);

        $conditionCombineMock = $this->createMock(CombineCondition::class);
        $conditionRuleMock->expects($this->once())
            ->method('setConditions')
            ->with([])
            ->willReturnSelf();
        $conditionRuleMock->expects($this->once())
            ->method('getConditions')
            ->willReturn($conditionCombineMock);

        if ($condition) {
            $this->conditionConverterMock->expects($this->once())
                ->method('dataModelToArray')
                ->with($condition)
                ->willReturn($conditionData);

            $conditionCombineMock->expects($this->once())
                ->method('loadArray')
                ->with($conditionData)
                ->willReturnSelf();
        } else {
            $conditionCombineMock->expects($this->once())
                ->method('asArray')
                ->willReturn($conditionData);
        }

        $this->assertSame($conditionRuleMock, $this->conditionLoader->loadRule($condition));
    }

    /**
     * @return array
     */
    public function loadRuleDataProvider()
    {
        return [
            ['condition' => $this->createMock(ConditionInterface::class)],
            ['condition' => null]
        ];
    }
}
