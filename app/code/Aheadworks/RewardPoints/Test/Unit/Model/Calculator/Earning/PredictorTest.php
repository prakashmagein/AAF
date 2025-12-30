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
namespace Aheadworks\RewardPoints\Test\Unit\Model\Calculator\Earning;

use Aheadworks\RewardPoints\Model\Calculator\Earning\Calculator\CalculationRequestInterface;
use Aheadworks\RewardPoints\Model\Calculator\Earning\Calculator\CalculationRequestInterfaceFactory;
use Aheadworks\RewardPoints\Model\Calculator\Earning\Calculator\General;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemInterface;
use Aheadworks\RewardPoints\Model\Calculator\Earning\Predictor;
use Aheadworks\RewardPoints\Model\Calculator\ResultInterface;
use Aheadworks\RewardPoints\Model\Config;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Model\Calculator\Earning\Predictor
 */
class PredictorTest extends TestCase
{
    /**
     * @var Predictor
     */
    private $predictor;

    /**
     * @var Config|MockObject
     */
    private $configMock;

    /**
     * @var General|MockObject
     */
    private $generalCalculatorMock;

    /**
     * @var CalculationRequestInterfaceFactory|MockObject
     */
    private $calculationRequestFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->configMock = $this->createMock(Config::class);
        $this->generalCalculatorMock = $this->createMock(General::class);
        $this->calculationRequestFactoryMock = $this->createMock(CalculationRequestInterfaceFactory::class);

        $this->predictor = $objectManager->getObject(
            Predictor::class,
            [
                'config' => $this->configMock,
                'generalCalculator' => $this->generalCalculatorMock,
                'calculationRequestFactory' => $this->calculationRequestFactoryMock,
            ]
        );
    }

    /**
     * Test calculateMaxPointsForCustomer method
     */
    public function testCalculateMaxPointsForCustomer()
    {
        $customerId = 10;
        $websiteId = 1;

        $calculationRequestMock = $this->getСalculationRequestMock($customerId, null, $websiteId, false);

        $earnItemFirstMock = $this->createMock(EarnItemInterface::class);
        $earnItemSecondMock = $this->createMock(EarnItemInterface::class);

        $earnItems = [$earnItemFirstMock, $earnItemSecondMock];

        $resultFirstMock = $this->getCalculationResultMock(100);
        $resultSecondMock = $this->getCalculationResultMock(102.5);

        $calculationRequestMock->expects($this->exactly(2))
            ->method('setItems')
            ->withConsecutive([[$earnItemFirstMock]], [[$earnItemSecondMock]])
            ->willReturnSelf();

        $this->generalCalculatorMock->expects($this->exactly(2))
            ->method('calculate')
            ->withConsecutive(
                [[$earnItemFirstMock], $customerId, $websiteId],
                [[$earnItemSecondMock], $customerId, $websiteId]
            )
            ->willReturnOnConsecutiveCalls($resultFirstMock, $resultSecondMock);

        $calculationRequestMock->expects($this->exactly(2))
            ->method('setPoints')
            ->withConsecutive([100], [102.5])
            ->willReturnSelf();

        $resultPointsByRulesMock = $this->createMock(ResultInterface::class);
        $this->generalCalculatorMock->expects($this->exactly(2))
            ->method('calculatePointsByRules')
            ->withConsecutive(
                [$resultFirstMock, $calculationRequestMock],
                [$resultSecondMock, $calculationRequestMock]
            )
            ->willReturnOnConsecutiveCalls($resultPointsByRulesMock, $resultPointsByRulesMock);

        $this->assertSame(
            $resultPointsByRulesMock,
            $this->predictor->calculateMaxPointsForCustomer($earnItems, $customerId, $websiteId)
        );
    }

    /**
     * Test calculateMaxPointsForCustomer method (other case)
     */
    public function testCalculateMaxPointsForCustomerOtherCase()
    {
        $customerId = 10;
        $websiteId = 2;

        $calculationRequestMock = $this->getСalculationRequestMock($customerId, null, $websiteId, false);

        $earnItemFirstMock = $this->createMock(EarnItemInterface::class);
        $earnItemSecondMock = $this->createMock(EarnItemInterface::class);
        $earnItems = [$earnItemFirstMock, $earnItemSecondMock];

        $resultFirstMock = $this->getCalculationResultMock(100);
        $resultSecondMock = $this->getCalculationResultMock(99);

        $calculationRequestMock->expects($this->exactly(2))
            ->method('setItems')
            ->withConsecutive([[$earnItemFirstMock]], [[$earnItemSecondMock]])
            ->willReturnSelf();

        $this->generalCalculatorMock->expects($this->exactly(2))
            ->method('calculate')
            ->withConsecutive(
                [[$earnItemFirstMock], $customerId, $websiteId],
                [[$earnItemSecondMock], $customerId, $websiteId]
            )
            ->willReturnOnConsecutiveCalls($resultFirstMock, $resultSecondMock);

        $calculationRequestMock->expects($this->exactly(2))
            ->method('setPoints')
            ->withConsecutive([100], [99])
            ->willReturnSelf();

        $resultPointsByRulesMock = $this->createMock(ResultInterface::class);
        $this->generalCalculatorMock->expects($this->exactly(2))
            ->method('calculatePointsByRules')
            ->withConsecutive(
                [$resultFirstMock, $calculationRequestMock],
                [$resultSecondMock, $calculationRequestMock]
            )
            ->willReturnOnConsecutiveCalls($resultPointsByRulesMock, $resultPointsByRulesMock);

        $this->assertSame(
            $resultPointsByRulesMock,
            $this->predictor->calculateMaxPointsForCustomer($earnItems, $customerId, $websiteId)
        );
    }

    /**
     * Test calculateMaxPointsForCustomer method if merge rule ids enabled
     */
    public function testCalculateMaxPointsForCustomerMergeRuleIds()
    {
        $customerId = 10;
        $websiteId = 2;
        $mergeRuleIds = true;

        $calculationRequestMock = $this->getСalculationRequestMock($customerId, null, $websiteId, false);

        $earnItemFirstMock = $this->createMock(EarnItemInterface::class);
        $earnItemSecondMock = $this->createMock(EarnItemInterface::class);
        $earnItemThirdMock = $this->createMock(EarnItemInterface::class);
        $earnItems = [$earnItemFirstMock, $earnItemSecondMock, $earnItemThirdMock];

        $resultFirstMock = $this->getCalculationResultMock(100, [1, 2]);
        $resultSecondMock = $this->getCalculationResultMock(102.5, [2, 3], [0 => 1, 1 => 2, 3 => 3]);
        $resultThirdMock = $this->getCalculationResultMock(99, []);

        $calculationRequestMock->expects($this->exactly(3))
            ->method('setItems')
            ->withConsecutive([[$earnItemFirstMock]], [[$earnItemSecondMock]],[[$earnItemThirdMock]])
            ->willReturnSelf();

        $calculationRequestMock->expects($this->exactly(3))
            ->method('setPoints')
            ->withConsecutive([100], [102.5], [99])
            ->willReturnSelf();

        $this->generalCalculatorMock->expects($this->exactly(3))
            ->method('calculate')
            ->withConsecutive(
                [[$earnItemFirstMock], $customerId, $websiteId],
                [[$earnItemSecondMock], $customerId, $websiteId],
                [[$earnItemThirdMock], $customerId, $websiteId]
            )
            ->willReturnOnConsecutiveCalls($resultFirstMock, $resultSecondMock, $resultThirdMock);

        $this->generalCalculatorMock->expects($this->exactly(3))
            ->method('calculatePointsByRules')
            ->withConsecutive(
                [$resultFirstMock, $calculationRequestMock],
                [$resultSecondMock, $calculationRequestMock],
                [$resultThirdMock, $calculationRequestMock]
            )
            ->willReturnOnConsecutiveCalls($resultFirstMock, $resultSecondMock, $resultThirdMock);

        $this->assertSame(
            $resultSecondMock,
            $this->predictor->calculateMaxPointsForCustomer($earnItems, $customerId, $websiteId, $mergeRuleIds)
        );
    }

    /**
     * Test calculateMaxPointsForCustomer method if no items specified
     */
    public function testCalculateMaxPointsForCustomerNoItems()
    {
        $customerId = 10;
        $websiteId = 2;
        $earnItems = [];

        $calculationRequestMock = $this->createMock(CalculationRequestInterface::class);
        $this->calculationRequestFactoryMock->expects($this->never())
            ->method('create');

        $calculationRequestMock->expects($this->never())
            ->method('setCustomerId');
        $calculationRequestMock->expects($this->never())
            ->method('setCustomerGroupId');
        $calculationRequestMock->expects($this->never())
            ->method('setWebsiteId');
        $calculationRequestMock->expects($this->never())
            ->method('setIsNeedCalculateCartRule');

        $resultMock = $this->createMock(ResultInterface::class);
        $calculationRequestMock->expects($this->never())
            ->method('setItems');
        $this->generalCalculatorMock->expects($this->never())
            ->method('calculate');
        $calculationRequestMock->expects($this->never())
            ->method('setPoints');
        $this->generalCalculatorMock->expects($this->never())
            ->method('calculatePointsByRules');
        $this->generalCalculatorMock->expects($this->once())
            ->method('getEmptyResult')
            ->willReturn($resultMock);

        $this->assertSame(
            $resultMock,
            $this->predictor->calculateMaxPointsForCustomer($earnItems, $customerId, $websiteId)
        );
    }

    /**
     * Test calculateMaxPointsForGuest method
     */
    public function testCalculateMaxPointsForGuest()
    {
        $customerGroupId = 10;
        $websiteId = 2;

        $this->configMock->expects($this->once())
            ->method('getDefaultCustomerGroupIdForGuest')
            ->willReturn($customerGroupId);

        $calculationRequestMock = $this->getСalculationRequestMock(null, $customerGroupId, $websiteId, false);

        $earnItemFirstMock = $this->createMock(EarnItemInterface::class);
        $earnItemSecondMock = $this->createMock(EarnItemInterface::class);
        $earnItems = [$earnItemFirstMock, $earnItemSecondMock];

        $resultFirstMock = $this->getCalculationResultMock(100);
        $resultSecondMock = $this->getCalculationResultMock(120);


        $calculationRequestMock->expects($this->exactly(2))
            ->method('setItems')
            ->withConsecutive([[$earnItemFirstMock]], [[$earnItemSecondMock]])
            ->willReturnSelf();

        $this->generalCalculatorMock->expects($this->exactly(2))
            ->method('calculateByCustomerGroup')
            ->withConsecutive(
                [[$earnItemFirstMock], $customerGroupId, $websiteId],
                [[$earnItemSecondMock], $customerGroupId, $websiteId]
            )
            ->willReturnOnConsecutiveCalls($resultFirstMock, $resultSecondMock);

        $calculationRequestMock->expects($this->exactly(2))
            ->method('setPoints')
            ->withConsecutive([100], [120])
            ->willReturnSelf();

        $resultPointsByRulesMock = $this->createMock(ResultInterface::class);
        $this->generalCalculatorMock->expects($this->exactly(2))
            ->method('calculatePointsByRules')
            ->withConsecutive(
                [$resultFirstMock, $calculationRequestMock],
                [$resultSecondMock, $calculationRequestMock]
            )
            ->willReturnOnConsecutiveCalls($resultPointsByRulesMock, $resultPointsByRulesMock);

        $this->assertSame(
            $resultPointsByRulesMock,
            $this->predictor->calculateMaxPointsForGuest($earnItems, $websiteId)
        );
    }

    /**
     * Test calculateMaxPointsForGuest method if merge rule ids enabled
     */
    public function testCalculateMaxPointsForGuestMergeRuleIds()
    {
        $customerGroupId = 10;
        $websiteId = 2;
        $mergeRuleIds = true;

        $this->configMock->expects($this->once())
            ->method('getDefaultCustomerGroupIdForGuest')
            ->willReturn($customerGroupId);

        $calculationRequestMock = $this->getСalculationRequestMock(null, $customerGroupId, $websiteId, false);

        $earnItemFirstMock = $this->createMock(EarnItemInterface::class);
        $earnItemSecondMock = $this->createMock(EarnItemInterface::class);
        $earnItemThirdMock = $this->createMock(EarnItemInterface::class);
        $earnItems = [$earnItemFirstMock, $earnItemSecondMock, $earnItemThirdMock];

        $resultFirstMock = $this->getCalculationResultMock(100, [1, 3]);
        $resultSecondMock = $this->getCalculationResultMock(120, [2], [1, 3, 2]);
        $resultThirdMock = $this->getCalculationResultMock(90, [2, 3]);

        $calculationRequestMock->expects($this->exactly(3))
            ->method('setItems')
            ->withConsecutive([[$earnItemFirstMock]], [[$earnItemSecondMock]],[[$earnItemThirdMock]])
            ->willReturnSelf();

        $this->generalCalculatorMock->expects($this->exactly(3))
            ->method('calculateByCustomerGroup')
            ->withConsecutive(
                [[$earnItemFirstMock], $customerGroupId, $websiteId],
                [[$earnItemSecondMock], $customerGroupId, $websiteId],
                [[$earnItemThirdMock], $customerGroupId, $websiteId]
            )
            ->willReturnOnConsecutiveCalls($resultFirstMock, $resultSecondMock, $resultThirdMock);

        $calculationRequestMock->expects($this->exactly(3))
            ->method('setPoints')
            ->withConsecutive([100], [120], [90])
            ->willReturnSelf();

        $this->generalCalculatorMock->expects($this->exactly(3))
            ->method('calculatePointsByRules')
            ->withConsecutive(
                [$resultFirstMock, $calculationRequestMock],
                [$resultSecondMock, $calculationRequestMock],
                [$resultThirdMock, $calculationRequestMock]
            )
            ->willReturnOnConsecutiveCalls($resultFirstMock, $resultSecondMock, $resultThirdMock);

        $this->assertSame(
            $resultSecondMock,
            $this->predictor->calculateMaxPointsForGuest($earnItems, $websiteId, $mergeRuleIds)
        );
    }

    /**
     * Test calculateMaxPointsForGuest method if no items specified
     */
    public function testCalculateMaxPointsForGuestNoItems()
    {
        $websiteId = 2;
        $earnItems = [];

        $calculationRequestMock = $this->createMock(CalculationRequestInterface::class);
        $this->calculationRequestFactoryMock->expects($this->never())
            ->method('create');

        $calculationRequestMock->expects($this->never())
            ->method('setCustomerId');
        $calculationRequestMock->expects($this->never())
            ->method('setCustomerGroupId');
        $calculationRequestMock->expects($this->never())
            ->method('setWebsiteId');
        $calculationRequestMock->expects($this->never())
            ->method('setIsNeedCalculateCartRule');

        $resultMock = $this->createMock(ResultInterface::class);

        $calculationRequestMock->expects($this->never())
            ->method('setItems');
        $this->generalCalculatorMock->expects($this->never())
            ->method('calculate');
        $calculationRequestMock->expects($this->never())
            ->method('setPoints');
        $this->generalCalculatorMock->expects($this->never())
            ->method('calculatePointsByRules');
        $this->generalCalculatorMock->expects($this->once())
            ->method('getEmptyResult')
            ->willReturn($resultMock);

        $this->assertSame(
            $resultMock,
            $this->predictor->calculateMaxPointsForGuest($earnItems, $websiteId)
        );
    }

    /**
     * Test calculateMaxPointsForCustomerGroup method
     */
    public function testCalculateMaxPointsForCustomerGroup()
    {
        $customerGroupId = 10;
        $websiteId = 2;

        $this->configMock->expects($this->never())
            ->method('getDefaultCustomerGroupIdForGuest');

        $calculationRequestMock = $this->getСalculationRequestMock(null, $customerGroupId, $websiteId, false);

        $earnItemFirstMock = $this->createMock(EarnItemInterface::class);
        $earnItemSecondMock = $this->createMock(EarnItemInterface::class);
        $earnItems = [$earnItemFirstMock, $earnItemSecondMock];

        $resultFirstMock = $this->getCalculationResultMock(100);
        $resultSecondMock = $this->getCalculationResultMock(120);

        $calculationRequestMock->expects($this->exactly(2))
            ->method('setItems')
            ->withConsecutive([[$earnItemFirstMock]], [[$earnItemSecondMock]])
            ->willReturnSelf();

        $this->generalCalculatorMock->expects($this->exactly(2))
            ->method('calculateByCustomerGroup')
            ->withConsecutive(
                [[$earnItemFirstMock], $customerGroupId, $websiteId],
                [[$earnItemSecondMock], $customerGroupId, $websiteId]
            )
            ->willReturnOnConsecutiveCalls($resultFirstMock, $resultSecondMock);

        $calculationRequestMock->expects($this->exactly(2))
            ->method('setPoints')
            ->withConsecutive([100], [120])
            ->willReturnSelf();

        $resultPointsByRulesMock = $this->createMock(ResultInterface::class);
        $this->generalCalculatorMock->expects($this->exactly(2))
            ->method('calculatePointsByRules')
            ->withConsecutive(
                [$resultFirstMock, $calculationRequestMock],
                [$resultSecondMock, $calculationRequestMock]
            )
            ->willReturnOnConsecutiveCalls($resultPointsByRulesMock, $resultPointsByRulesMock);

        $this->assertSame(
            $resultPointsByRulesMock,
            $this->predictor->calculateMaxPointsForCustomerGroup($earnItems, $websiteId, $customerGroupId)
        );
    }

    /**
     * Test calculateMaxPointsForCustomerGroup method if merge rule ids enabled
     */
    public function testCalculateMaxPointsForCustomerGroupMergeRuleIds()
    {
        $customerGroupId = 10;
        $websiteId = 2;
        $mergeRuleIds = true;

        $this->configMock->expects($this->never())
            ->method('getDefaultCustomerGroupIdForGuest');

        $calculationRequestMock = $this->getСalculationRequestMock(null, $customerGroupId, $websiteId, false);

        $earnItemFirstMock = $this->createMock(EarnItemInterface::class);
        $earnItemSecondMock = $this->createMock(EarnItemInterface::class);
        $earnItemThirdMock = $this->createMock(EarnItemInterface::class);
        $earnItems = [$earnItemFirstMock, $earnItemSecondMock, $earnItemThirdMock];

        $resultFirstMock = $this->getCalculationResultMock(100, [1, 3]);
        $resultSecondMock = $this->getCalculationResultMock(120, [2], [1, 3, 2]);
        $resultThirdMock = $this->getCalculationResultMock(90, [2, 3]);

        $calculationRequestMock->expects($this->exactly(3))
            ->method('setItems')
            ->withConsecutive([[$earnItemFirstMock]], [[$earnItemSecondMock]], [[$earnItemThirdMock]])
            ->willReturnSelf();

        $this->generalCalculatorMock->expects($this->exactly(3))
            ->method('calculateByCustomerGroup')
            ->withConsecutive(
                [[$earnItemFirstMock], $customerGroupId, $websiteId],
                [[$earnItemSecondMock], $customerGroupId, $websiteId],
                [[$earnItemThirdMock], $customerGroupId, $websiteId]
            )
            ->willReturnOnConsecutiveCalls($resultFirstMock, $resultSecondMock, $resultThirdMock);

        $calculationRequestMock->expects($this->exactly(3))
            ->method('setPoints')
            ->withConsecutive([100], [120], [90])
            ->willReturnSelf();

        $this->generalCalculatorMock->expects($this->exactly(3))
            ->method('calculatePointsByRules')
            ->withConsecutive(
                [$resultFirstMock, $calculationRequestMock],
                [$resultSecondMock, $calculationRequestMock],
                [$resultThirdMock, $calculationRequestMock]
            )
            ->willReturnOnConsecutiveCalls($resultFirstMock, $resultSecondMock, $resultThirdMock);

        $this->assertSame(
            $resultSecondMock,
            $this->predictor->calculateMaxPointsForCustomerGroup(
                $earnItems,
                $websiteId,
                $customerGroupId,
                $mergeRuleIds
            )
        );
    }

    /**
     * Test calculateMaxPointsForCustomerGroup method if no items specified
     */
    public function testCalculateMaxPointsForCustomerGroupNoItems()
    {
        $customerGroupId = 12;
        $websiteId = 2;
        $earnItems = [];

        $calculationRequestMock = $this->createMock(CalculationRequestInterface::class);
        $this->calculationRequestFactoryMock->expects($this->never())
            ->method('create');

        $calculationRequestMock->expects($this->never())
            ->method('setCustomerId');
        $calculationRequestMock->expects($this->never())
            ->method('setCustomerGroupId');
        $calculationRequestMock->expects($this->never())
            ->method('setWebsiteId');
        $calculationRequestMock->expects($this->never())
            ->method('setIsNeedCalculateCartRule');

        $this->configMock->expects($this->never())
            ->method('getDefaultCustomerGroupIdForGuest');

        $resultMock = $this->createMock(ResultInterface::class);

        $calculationRequestMock->expects($this->never())
            ->method('setItems');
        $this->generalCalculatorMock->expects($this->never())
            ->method('calculate');
        $calculationRequestMock->expects($this->never())
            ->method('setPoints');
        $this->generalCalculatorMock->expects($this->never())
            ->method('calculatePointsByRules');
        $this->generalCalculatorMock->expects($this->once())
            ->method('getEmptyResult')
            ->willReturn($resultMock);

        $this->assertSame(
            $resultMock,
            $this->predictor->calculateMaxPointsForCustomerGroup($earnItems, $websiteId, $customerGroupId)
        );
    }

    /**
     * Get calculation result mock
     *
     * @param float $points
     * @param int[] $ruleIds
     * @return ResultInterface|MockObject
     */
    private function getCalculationResultMock($points, $ruleIds = [], $newRuleIds = [])
    {
        $resultMock = $this->createMock(ResultInterface::class);
        $resultMock->expects($this->any())
            ->method('getPoints')
            ->willReturn($points);
        $resultMock->expects($this->any())
            ->method('getAppliedRuleIds')
            ->willReturn($ruleIds);

        if (!empty($newRuleIds)) {
            $resultMock->expects($this->once())
                ->method('setAppliedRuleIds')
                ->with($newRuleIds)
                ->willReturnSelf($newRuleIds);
        }

        return $resultMock;
    }

    /**
     * Get СalculationRequestMock
     *
     * @param int|null $customerId
     * @param int|null $customerGroupId
     * @param int $websiteId
     * @param bool $isNeedCalculateCartRule
     * @return CalculationRequestInterface|MockObject
     */
    private function getСalculationRequestMock($customerId, $customerGroupId, $websiteId, $isNeedCalculateCartRule)
    {
        $calculationRequestMock = $this->createMock(CalculationRequestInterface::class);
        $this->calculationRequestFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($calculationRequestMock);

        $calculationRequestMock->expects($this->once())
            ->method('setCustomerId')
            ->with($customerId)
            ->willReturnSelf();
        $calculationRequestMock->expects($this->once())
            ->method('setCustomerGroupId')
            ->with($customerGroupId)
            ->willReturnSelf();
        $calculationRequestMock->expects($this->once())
            ->method('setWebsiteId')
            ->with($websiteId)
            ->willReturnSelf();
        $calculationRequestMock->expects($this->once())
            ->method('setIsNeedCalculateCartRule')
            ->with($isNeedCalculateCartRule)
            ->willReturnSelf();

        return $calculationRequestMock;
    }
}
