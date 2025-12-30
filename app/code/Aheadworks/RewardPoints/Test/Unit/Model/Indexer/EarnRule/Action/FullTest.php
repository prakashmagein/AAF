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
namespace Aheadworks\RewardPoints\Test\Unit\Model\Indexer\EarnRule\Action;

use Aheadworks\RewardPoints\Model\Indexer\EarnRule\Action\Full as FullIndexer;
use Aheadworks\RewardPoints\Model\ResourceModel\EarnRule\Indexer\Product as EarnRuleProductIndexerResource;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Model\Indexer\EarnRule\Action\Full
 */
class FullTest extends TestCase
{
    /**
     * @var FullIndexer
     */
    private $indexer;

    /**
     * @var EarnRuleProductIndexerResource|MockObject
     */
    private $earnRuleProductIndexerResourceMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->earnRuleProductIndexerResourceMock = $this->createMock(EarnRuleProductIndexerResource::class);

        $this->indexer = $objectManager->getObject(
            FullIndexer::class,
            [
                'earnRuleProductIndexerResource' => $this->earnRuleProductIndexerResourceMock,
            ]
        );
    }

    /**
     * Test execute method
     */
    public function testExecute()
    {
        $this->earnRuleProductIndexerResourceMock->expects($this->once())
            ->method('reindexAll')
            ->willReturnSelf();

        $this->assertNull($this->indexer->execute());
    }

    /**
     * Test execute method if incorrect ids specified
     *
     * @param array|null $rowIds
     * @dataProvider executeIncorrectIdsDataProvider
     */
    public function testExecuteIncorrectIds($rowIds)
    {
        $this->earnRuleProductIndexerResourceMock->expects($this->once())
            ->method('reindexAll')
            ->willReturnSelf();

        $this->assertNull($this->indexer->execute($rowIds));
    }

    /**
     * @return array
     */
    public function executeIncorrectIdsDataProvider()
    {
        return [
            ['rowIds' => null],
            ['rowIds' => []],
            ['rowId' => 0]
        ];
    }

    /**
     * Test execute method if an error occurs
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Error!
     */
    public function testExecuteError()
    {
        $errorMessage = 'Error!';

        $this->earnRuleProductIndexerResourceMock->expects($this->once())
            ->method('reindexAll')
            ->willThrowException(new \Exception($errorMessage));
$this->expectException(\Exception::class);
        $this->indexer->execute();
    }
}
