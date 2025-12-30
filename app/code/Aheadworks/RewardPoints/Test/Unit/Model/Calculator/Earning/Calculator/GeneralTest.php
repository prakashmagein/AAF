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
namespace Aheadworks\RewardPoints\Test\Unit\Model\Calculator\Earning\Calculator;

use Aheadworks\RewardPoints\Api\Data\EarnRateInterface;
use Aheadworks\RewardPoints\Api\EarnRuleManagementInterface;
use Aheadworks\RewardPoints\Model\Calculator\Earning\Calculator\General;
use Aheadworks\RewardPoints\Model\Calculator\Earning\Calculator\Pool;
use Aheadworks\RewardPoints\Model\Calculator\Earning\Calculator\RateResolver;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemInterface;
use Aheadworks\RewardPoints\Model\Calculator\RateCalculator;
use Aheadworks\RewardPoints\Model\Calculator\ResultInterface;
use Aheadworks\RewardPoints\Model\Calculator\ResultInterfaceFactory;
use Aheadworks\RewardPoints\Model\Calculator\Earning\Calculator\Rounding;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface as Logger;

/**
 * Test for \Aheadworks\RewardPoints\Model\Calculator\Earning\Calculator\General
 */
class GeneralTest extends TestCase
{
    /**
     * @var General
     */
    private $calculator;

    /**
     * @var RateCalculator|MockObject
     */
    private $rateCalculatorMock;

    /**
     * @var RateResolver|MockObject
     */
    private $rateResolverMock;

    /**
     * @var Pool|MockObject
     */
    private $calculatorPoolMock;

    /**
     * @var ResultInterfaceFactory|MockObject
     */
    private $resultFactoryMock;

    /**
     * @var Logger|MockObject
     */
    private $loggerMock;

    /**
     * @var EarnRuleManagementInterface|MockObject
     */
    private $earnRuleManagementMock;

    /**
     * @var Rounding|MockObject
     */
    private $roundingMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->rateCalculatorMock = $this->createMock(RateCalculator::class);
        $this->rateResolverMock = $this->createMock(RateResolver::class);
        $this->calculatorPoolMock = $this->createMock(Pool::class);
        $this->resultFactoryMock = $this->createMock(ResultInterfaceFactory::class);
        $this->loggerMock = $this->createMock(Logger::class);
        $this->roundingMock = $this->createMock(Rounding::class);
        $this->earnRuleManagementMock = $this->createMock(EarnRuleManagementInterface::class);

        $this->calculator = $objectManager->getObject(
            General::class,
            [
                'rateCalculator' => $this->rateCalculatorMock,
                'rateResolver' => $this->rateResolverMock,
                'resultFactory' => $this->resultFactoryMock,
                'calculatorPool' => $this->calculatorPoolMock,
                'logger' => $this->loggerMock,
                'earnRuleManagement' => $this->earnRuleManagementMock,
                'rounding' => $this->roundingMock
            ]
        );
    }

    /**
     * Test calculate method
     */
    public function testCalculate()
    {
        $items = [
            $this->getEarnItemMock(125, 20.5, 2),
            $this->getEarnItemMock(126, 10, 1)
        ];
        $customerId = 11;
        $websiteId = 3;

        $this->rateCalculatorMock->expects($this->exactly(2))
            ->method('calculateEarnPointsRaw')
            ->withConsecutive(
                [$customerId, 20.5, $websiteId],
                [$customerId, 10, $websiteId]
            )
            ->willReturnOnConsecutiveCalls(205, 100);

        $this->roundingMock->expects($this->exactly(2))
            ->method('apply')
            ->withConsecutive(
                [205, $websiteId],
                [100, $websiteId]
            )->willReturnOnConsecutiveCalls(205, 100);

        $resultMock = $this->getResultMock(305, [], true);
        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultMock);

        $this->assertEquals($resultMock, $this->calculator->calculate($items, $customerId, $websiteId));
    }

    /**
     * Test calculate method when need to round points amounts for multiple items
     */
    public function testCalculateRoundAmountsForMultipleItems()
    {
        $items = [
            $this->getEarnItemMock(11, 27.5, 1),
            $this->getEarnItemMock(12, 30, 1),
            $this->getEarnItemMock(13, 37.5, 1)
        ];
        $customerId = 1;
        $websiteId = 1;

        $this->rateCalculatorMock->expects($this->exactly(3))
            ->method('calculateEarnPointsRaw')
            ->withConsecutive(
                [$customerId, 27.5, $websiteId],
                [$customerId, 30, $websiteId],
                [$customerId, 37.5, $websiteId]
            )
            ->willReturnOnConsecutiveCalls(2.75, 3, 3.75);

        $this->roundingMock->expects($this->exactly(3))
            ->method('apply')
            ->withConsecutive(
                [2.75, $websiteId],
                [3, $websiteId],
                [3.75, $websiteId]
            )->willReturnOnConsecutiveCalls(3, 3, 4);

        $resultMock = $this->getResultMock(10, [], true);
        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultMock);

        $this->assertEquals($resultMock, $this->calculator->calculate($items, $customerId, $websiteId));
    }

    /**
     * Test calculate method if no items specified
     */
    public function testCalculateNoItems()
    {
        $items = [];
        $customerId = 11;
        $websiteId = 3;

        $this->rateCalculatorMock->expects($this->never())
            ->method('calculateEarnPointsRaw');


        $resultMock = $this->getResultMock(0, [], true);
        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultMock);

        $this->assertEquals($resultMock, $this->calculator->calculate($items, $customerId, $websiteId));
    }

    /**
     * Test calculate method if empty item specified
     */
    public function testCalculateEmptyItem()
    {
        $items = [
            $this->getEarnItemMock(null, 0, 0),
        ];
        $customerId = 11;
        $websiteId = 3;

        $this->rateCalculatorMock->expects($this->once())
            ->method('calculateEarnPointsRaw')
            ->with($customerId, 0, $websiteId)
            ->willReturn(0);


        $resultMock = $this->getResultMock(0, [], true);
        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultMock);

        $this->assertEquals($resultMock, $this->calculator->calculate($items, $customerId, $websiteId));
    }

    /**
     * Test calculateByCustomerGroup method
     */
    public function testCalculateByCustomerGroup()
    {
        $items = [
            $this->getEarnItemMock(125, 20.5, 2),
            $this->getEarnItemMock(126, 10, 1)
        ];
        $customerGroupId = 5;
        $websiteId = 3;

        $earnRateMock = $this->createMock(EarnRateInterface::class);
        $this->rateResolverMock->expects($this->once())
            ->method('getEarnRate')
            ->with($customerGroupId, $websiteId)
            ->willReturn($earnRateMock);
        $this->rateCalculatorMock->expects($this->exactly(2))
            ->method('calculateEarnPointsByRateRaw')
            ->withConsecutive(
                [$earnRateMock, 20.5],
                [$earnRateMock, 10]
            )
            ->willReturnOnConsecutiveCalls(205, 100);

        $this->roundingMock->expects($this->exactly(2))
            ->method('apply')
            ->withConsecutive(
                [205, $websiteId],
                [100, $websiteId]
            )->willReturnOnConsecutiveCalls(205, 100);

        $resultMock = $this->getResultMock(305, [], true);
        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultMock);

        $this->assertEquals(
            $resultMock,
            $this->calculator->calculateByCustomerGroup($items, $customerGroupId, $websiteId)
        );
    }

    /**
     * Test calculateByCustomerGroup method if no rate found
     */
    public function testCalculateByCustomerGroupNoRate()
    {
        $items = [
            $this->getEarnItemMock(125, 20.5, 2),
            $this->getEarnItemMock(126, 10, 1)
        ];
        $customerGroupId = 5;
        $websiteId = 3;

        $this->rateResolverMock->expects($this->once())
            ->method('getEarnRate')
            ->with($customerGroupId, $websiteId)
            ->willReturn(null);
        $this->rateCalculatorMock->expects($this->never())
            ->method('calculateEarnPointsByRateRaw');


        $resultMock = $this->getResultMock(0, [], true);
        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultMock);

        $this->assertEquals(
            $resultMock,
            $this->calculator->calculateByCustomerGroup($items, $customerGroupId, $websiteId)
        );
    }

    /**
     * Test calculateByCustomerGroup method if no items specified
     */
    public function testCalculateByCustomerGroupNoItems()
    {
        $items = [];
        $customerGroupId = 5;
        $websiteId = 3;

        $earnRateMock = $this->createMock(EarnRateInterface::class);
        $this->rateResolverMock->expects($this->once())
            ->method('getEarnRate')
            ->with($customerGroupId, $websiteId)
            ->willReturn($earnRateMock);

        $this->rateCalculatorMock->expects($this->never())
            ->method('calculateEarnPointsByRateRaw');


        $resultMock = $this->getResultMock(0, [], true);
        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultMock);

        $this->assertEquals(
            $resultMock,
            $this->calculator->calculateByCustomerGroup($items, $customerGroupId, $websiteId)
        );
    }

    /**
     * Test calculateByCustomerGroup method if empty item specified
     */
    public function testCalculateByCustomerGroupEmptyItem()
    {
        $items = [
            $this->getEarnItemMock(null, 0, 0),
        ];
        $customerGroupId = 5;
        $websiteId = 3;

        $earnRateMock = $this->createMock(EarnRateInterface::class);
        $this->rateResolverMock->expects($this->once())
            ->method('getEarnRate')
            ->with($customerGroupId, $websiteId)
            ->willReturn($earnRateMock);

        $this->rateCalculatorMock->expects($this->once())
            ->method('calculateEarnPointsByRateRaw')
            ->with($earnRateMock, 0)
            ->willReturn(0);


        $resultMock = $this->getResultMock(0, [], true);
        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultMock);

        $this->assertEquals(
            $resultMock,
            $this->calculator->calculateByCustomerGroup($items, $customerGroupId, $websiteId)
        );
    }

    /**
     * Test getEmptyResult method
     */
    public function testGetEmptyResult()
    {
        $resultMock = $this->getResultMock(0, [], true);
        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultMock);

        $this->assertEquals($resultMock, $this->calculator->getEmptyResult());
    }

    /**
     * Get earn item mock
     *
     * @param int $productId
     * @param float $baseAmount
     * @param float $qty
     * @return EarnItemInterface|MockObject
     */
    private function getEarnItemMock($productId, $baseAmount, $qty)
    {
        $itemMock = $this->createMock(EarnItemInterface::class);
        $itemMock->expects($this->any())
            ->method('getProductId')
            ->willReturn($productId);
        $itemMock->expects($this->any())
            ->method('getBaseAmount')
            ->willReturn($baseAmount);
        $itemMock->expects($this->any())
            ->method('getQty')
            ->willReturn($qty);

        return $itemMock;
    }

    /**
     * Get result mock
     *
     * @param float $pointsFinal
     * @param int[] $appliedRuleIds
     * @param bool|false $forceSet
     * @return ResultInterface|MockObject
     */
    private function getResultMock($pointsFinal, $appliedRuleIds, $forceSet = false)
    {
        $resultMock = $this->createMock(ResultInterface::class);
        if ($forceSet) {
            $resultMock->expects($this->once())
                ->method('setPoints')
                ->with($pointsFinal)
                ->willReturnSelf();
            $resultMock->expects($this->once())
                ->method('setAppliedRuleIds')
                ->with($appliedRuleIds)
                ->willReturnSelf();
        } else {
            $resultMock->expects($this->any())
                ->method('getPoints')
                ->willReturn($pointsFinal);
            $resultMock->expects($this->any())
                ->method('getAppliedRuleIds')
                ->willReturn($appliedRuleIds);
        }

        return $resultMock;
    }
}
