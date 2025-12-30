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
namespace Aheadworks\RewardPoints\Test\Unit\Model\Repository\CollectionProcessor;

use Aheadworks\RewardPoints\Model\Repository\CollectionProcessor\PaginationProcessor;
use Aheadworks\RewardPoints\Model\ResourceModel\AbstractCollection;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Model\Repository\CollectionProcessor\PaginationProcessor
 */
class PaginationProcessorTest extends TestCase
{
    /**
     * @var PaginationProcessor
     */
    private $processor;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->processor = $objectManager->getObject(PaginationProcessor::class, []);
    }

    /**
     * Test process method
     *
     * @param int|null $currentPage
     * @param int|null $pageSize
     * @dataProvider processDataProvider
     */
    public function testProcess($currentPage, $pageSize)
    {
        $searchCriteriaMock = $this->createMock(SearchCriteria::class);
        $searchCriteriaMock->expects($this->once())
            ->method('getCurrentPage')
            ->willReturn($currentPage);
        $searchCriteriaMock->expects($this->once())
            ->method('getPageSize')
            ->willReturn($pageSize);

        $collectionMock = $this->createMock(AbstractCollection::class);
        $collectionMock->expects($this->once())
            ->method('setCurPage')
            ->with($currentPage)
            ->willReturnSelf();
        $collectionMock->expects($this->once())
            ->method('setPageSize')
            ->with($pageSize)
            ->willReturnSelf();

        $this->assertNull($this->processor->process($searchCriteriaMock, $collectionMock));
    }

    /**
     * @return array
     */
    public function processDataProvider()
    {
        return [
            [
                'currentPage' => 10,
                'pageSize' => 20
            ],
            [
                'currentPage' => null,
                'pageSize' => null
            ],
        ];
    }
}
