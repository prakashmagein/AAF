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

use Aheadworks\RewardPoints\Model\Indexer\EarnRule\Action\Row as RowIndexer;
use Aheadworks\RewardPoints\Model\ResourceModel\EarnRule\Indexer\Product as EarnRuleProductIndexerResource;
use Magento\Framework\Exception\InputException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Model\Indexer\EarnRule\Action\Row
 */
class RowTest extends TestCase
{
    /**
     * @var RowIndexer
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
            RowIndexer::class,
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
        $rowId = 125;

        $this->earnRuleProductIndexerResourceMock->expects($this->once())
            ->method('reindexRows')
            ->with([$rowId])
            ->willReturnSelf();

        $this->assertNull($this->indexer->execute($rowId));
    }

    /**
     * Test execute method if an incorrect id specified
     *
     * @param int|string|null $rowId
     * @dataProvider executeIncorrectIdDataProvider
     * @expectedException \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage We can't rebuild the index for an undefined entity.
     */
    public function testExecuteIncorrectId($rowId)
    {
        $this->earnRuleProductIndexerResourceMock->expects($this->never())
            ->method('reindexRows');
        $this->expectException(InputException::class);
        $this->indexer->execute($rowId);
    }

    /**
     * @return array
     */
    public function executeIncorrectIdDataProvider()
    {
        return [
            ['rowId' => null],
            ['rowId' => ''],
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
        $rowId = 125;
        $errorMessage = 'Error!';

        $this->earnRuleProductIndexerResourceMock->expects($this->once())
            ->method('reindexRows')
            ->with([$rowId])
            ->willThrowException(new \Exception($errorMessage));
$this->expectException(\Exception::class);
        $this->indexer->execute($rowId);
    }
}
