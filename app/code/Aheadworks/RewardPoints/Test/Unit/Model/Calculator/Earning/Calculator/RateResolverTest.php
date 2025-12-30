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
use Aheadworks\RewardPoints\Api\Data\EarnRateSearchResultsInterface;
use Aheadworks\RewardPoints\Api\EarnRateRepositoryInterface;
use Aheadworks\RewardPoints\Model\Calculator\Earning\Calculator\RateResolver;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Model\Calculator\Earning\Calculator\RateResolver
 */
class RateResolverTest extends TestCase
{
    /**
     * @var RateResolver
     */
    private $resolver;

    /**
     * @var EarnRateRepositoryInterface|MockObject
     */
    private $earnRateRepositoryMock;

    /**
     * @var SearchCriteriaBuilder|MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->earnRateRepositoryMock = $this->createMock(EarnRateRepositoryInterface::class);
        $this->searchCriteriaBuilderMock = $this->createMock(SearchCriteriaBuilder::class);

        $this->resolver = $objectManager->getObject(
            RateResolver::class,
            [
                'earnRateRepository' => $this->earnRateRepositoryMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
            ]
        );
    }

    /**
     * Test getEarnRate method
     *
     * @param EarnRateInterface[] $earnRates
     * @param EarnRateInterface $result
     * @dataProvider getEarnRateDataProvider
     */
    public function testGetEarnRate($earnRates, $result)
    {
        $customerGroupId = 10;
        $websiteId = 2;

        $searchCriteriaMock = $this->createMock(SearchCriteria::class);
        $this->searchCriteriaBuilderMock->expects($this->exactly(2))
            ->method('addFilter')
            ->withConsecutive(
                [EarnRateInterface::CUSTOMER_GROUP_ID, [GroupInterface::CUST_GROUP_ALL, $customerGroupId], 'in'],
                [EarnRateInterface::WEBSITE_ID, $websiteId]
            )
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $searchResultMock = $this->createMock(EarnRateSearchResultsInterface::class);
        $this->earnRateRepositoryMock->expects($this->once())
            ->method('getList')
            ->with()
            ->willReturn($searchResultMock);

        $searchResultMock->expects($this->once())
            ->method('getItems')
            ->willReturn($earnRates);

        $this->assertSame($result, $this->resolver->getEarnRate($customerGroupId, $websiteId));
    }

    /**
     * @return array
     */
    public function getEarnRateDataProvider()
    {
        $earnRateFirst = $this->getEarnRateMock(100, 100);
        $earnRateSecond = $this->getEarnRateMock(50, 100);
        $earnRateThird = $this->getEarnRateMock(150, 100);
        return [
            [
                'earnRates' => [$earnRateFirst, $earnRateSecond, $earnRateThird],
                'result' => $earnRateSecond
            ],
            [
                'earnRates' => [$earnRateFirst,$earnRateThird, $earnRateSecond],
                'result' => $earnRateSecond
            ],
            [
                'earnRates' => [$earnRateSecond, $earnRateFirst, $earnRateThird],
                'result' => $earnRateSecond
            ],
            [
                'earnRates' => [$earnRateThird, $earnRateFirst],
                'result' => $earnRateFirst
            ],
            [
                'earnRates' => [$earnRateFirst, $earnRateThird],
                'result' => $earnRateFirst
            ],
            [
                'earnRates' => [$earnRateFirst],
                'result' => $earnRateFirst
            ],
            [
                'earnRates' => [],
                'result' => null
            ],
        ];
    }

    /**
     * Get earn rate mock
     *
     * @param float $baseAmount
     * @param int $points
     * @return EarnRateInterface|MockObject
     */
    private function getEarnRateMock($baseAmount, $points)
    {
        $earnRateMock = $this->createMock(EarnRateInterface::class);
        $earnRateMock->expects($this->any())
            ->method('getBaseAmount')
            ->willReturn($baseAmount);
        $earnRateMock->expects($this->any())
            ->method('getPoints')
            ->willReturn($points);

        return $earnRateMock;
    }
}
