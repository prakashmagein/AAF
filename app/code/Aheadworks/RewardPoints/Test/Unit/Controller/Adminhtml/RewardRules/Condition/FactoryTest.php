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
namespace Aheadworks\RewardPoints\Test\Unit\Controller\Adminhtml\RewardRules\Condition;

use Aheadworks\RewardPoints\Controller\Adminhtml\RewardRules\Condition\Factory as RuleConditionFactory;
use Magento\CatalogRule\Model\Rule;
use Magento\CatalogRule\Model\Rule\Condition\Product as ProductCondition;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\ConfigurationMismatchException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Controller\Adminhtml\RewardRules\Condition\Factory
 */
class FactoryTest extends TestCase
{
    /**
     * @var RuleConditionFactory
     */
    private $factory;

    /**
     * @var ObjectManagerInterface|MockObject
     */
    private $objectManagerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->objectManagerMock = $this->createMock(ObjectManagerInterface::class);

        $this->factory = $objectManager->getObject(
            RuleConditionFactory::class,
            [
                'objectManager' => $this->objectManagerMock,
            ]
        );
    }

    /**
     * Test process method
     *
     * @param string|null $attribute
     * @dataProvider processDataProvider
     * @throws \Exception
     */
    public function testProcess($attribute)
    {
        $type = ProductCondition::class;
        $id = '1--1';
        $prefix = 'conditions';
        $jsFormObject = 'rule_conditions_fieldset';
        $formName = 'aw_reward_points_earning_rules_form';

        $conditionMock = $this->createMock($type);
        $ruleMock = $this->createMock(Rule::class);

        $this->setupMocks($conditionMock, $id, $type, $ruleMock, $prefix, $attribute, $jsFormObject, $formName);

        $this->objectManagerMock->expects($this->exactly(2))
            ->method('create')
            ->withConsecutive([ProductCondition::class], [Rule::class])
            ->willReturnOnConsecutiveCalls($conditionMock, $ruleMock);

        $this->assertEquals(
            $conditionMock,
            $this->factory->create($type, $id, $prefix, $attribute, $jsFormObject, $formName)
        );
    }

    /**
     * Set up mocks
     *
     * @param ConditionCombine|MockObject $conditionMock
     * @param string $id
     * @param string $type
     * @param Rule|MockObject $ruleMock
     * @param string $prefix
     * @param string|null $attribute
     * @param string $jsFormObject
     * @param string $formName
     */
    private function setupMocks($conditionMock, $id, $type, $ruleMock, $prefix, $attribute, $jsFormObject, $formName)
    {
        $conditionMock->expects($this->any())
                      ->method('__call')
                      ->willReturnSelf();
    }

    /**
     * @return array
     */
    public function processDataProvider()
    {
        return [
            ['attribute' => 'value'],
            ['attribute' => null]
        ];
    }

    /**
     * Test process method if incorrect condition specified
     */
    public function testProcessIncorrectCondition()
    {
        $type = DataObject::class;
        $id = '1--1';
        $prefix = 'conditions';
        $attribute = null;
        $jsFormObject = 'rule_conditions_fieldset';
        $formName = 'aw_reward_points_earning_rules_form';

        $dataObjectMock = $this->createMock($type);

        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with(DataObject::class)
            ->willReturn($dataObjectMock);

        $this->expectException(ConfigurationMismatchException::class);

        $this->factory->create($type, $id, $prefix, $attribute, $jsFormObject, $formName);
    }
}
