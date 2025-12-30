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
declare(strict_types=1);

namespace Aheadworks\RewardPoints\Test\Unit\Block\Adminhtml\EarnRule\Edit\Conditions\Form;

use Aheadworks\RewardPoints\Api\Data\ConditionInterface;
use Aheadworks\RewardPoints\Api\Data\EarnRuleInterface;
use Aheadworks\RewardPoints\Api\Data\EarnRuleInterfaceFactory;
use Aheadworks\RewardPoints\Block\Adminhtml\EarnRule\Edit\Conditions\DataProvider;
use Aheadworks\RewardPoints\Block\Adminhtml\EarnRule\Edit\Conditions\TypeResolver;
use Aheadworks\RewardPoints\Controller\Adminhtml\Earning\Rules\Edit as RuleEditAction;
use Aheadworks\RewardPoints\Ui\DataProvider\EarnRule\FormDataProvider as EarnRuleFormDataProvider;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Block\Adminhtml\EarnRule\Edit\Conditions\DataProvider
 */
class DataProviderTest extends TestCase
{
    /**
     * @var DataProvider
     */
    private $dataProvider;

    /**
     * @var DataPersistorInterface|MockObject
     */
    private $dataPersistorMock;

    /**
     * @var Registry|MockObject
     */
    private $registryMock;

    /**
     * @var DataObjectProcessor|MockObject
     */
    private $dataObjectProcessorMock;

    /**
     * @var EarnRuleInterfaceFactory|MockObject
     */
    private $ruleFactoryMock;

    /**
     * @var DataObjectHelper|MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var TypeResolver|MockObject
     */
    private $typeResolverMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->dataPersistorMock = $this->createMock(DataPersistorInterface::class);
        $this->registryMock = $this->createMock(Registry::class);
        $this->dataObjectProcessorMock = $this->createMock(DataObjectProcessor::class);
        $this->ruleFactoryMock = $this->createMock(EarnRuleInterfaceFactory::class);
        $this->dataObjectHelperMock = $this->createMock(DataObjectHelper::class);
        $this->typeResolverMock = $this->createMock(TypeResolver::class);

        $this->dataProvider = $objectManager->getObject(
            DataProvider::class,
            [
                'dataPersistor' => $this->dataPersistorMock,
                'dataObjectProcessor' => $this->dataObjectProcessorMock,
                'coreRegistry' => $this->registryMock,
                'ruleFactory' => $this->ruleFactoryMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'typeResolver' => $this->typeResolverMock,
            ]
        );
    }

    /**
     * Test getConditionRule method if no rule in registry
     */
    public function testGetConditionsOnNull(): void
    {
        $conditions = null;
        $this->dataPersistorMock->expects($this->once())
            ->method('get')
            ->with(EarnRuleFormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY)
            ->willReturn(null);

        $this->registryMock->expects($this->once())
            ->method('registry')
            ->with(RuleEditAction::CURRENT_RULE_KEY)
            ->willReturn(null);

        $this->assertEquals($conditions, $this->dataProvider->getConditions('catalog'));
    }

    /**
     * Test testGetConditionsOnResult method if has rule in registry
     */
    public function testGetConditionsOnResult(): void
    {
        $conditions = [
            'value' => '1'
        ];
        $type = 'catalog';
        $this->dataPersistorMock->expects($this->once())
            ->method('get')
            ->with(EarnRuleFormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY)
            ->willReturn(null);

        $ruleMock = $this->createMock(EarnRuleInterface::class);
        $this->registryMock->expects($this->once())
            ->method('registry')
            ->with(RuleEditAction::CURRENT_RULE_KEY)
            ->willReturn($ruleMock);

        $conditionMock = $this->createMock(ConditionInterface::class);
        $ruleMock->expects($this->once())
            ->method('getCondition')
            ->willReturn($conditionMock);

        $ruleMock->expects($this->once())
            ->method('getType')
            ->willReturn($type);

        $this->typeResolverMock->expects($this->once())
            ->method('resolve')
            ->with($type)
            ->willReturn([$type]);

        $ruleData = [
            EarnRuleInterface::CONDITION => $conditions
        ];
        $this->dataObjectProcessorMock->expects($this->once())
            ->method('buildOutputDataArray')
            ->with($ruleMock,EarnRuleInterface::class)
            ->willReturn($ruleData);

        $this->assertEquals($conditions, $this->dataProvider->getConditions($type));
    }
}
