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

use Aheadworks\RewardPoints\Model\Indexer\EarnRule\Action\Rows as RowsIndexer;
use Aheadworks\RewardPoints\Model\ResourceModel\EarnRule\Indexer\Product as EarnRuleProductIndexerResource;
use Magento\Framework\Exception\InputException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Model\Indexer\EarnRule\Action\Rows
 */
class RowsTest extends TestCase
{
    /**
     * @var RowsIndexer
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
            RowsIndexer::class,
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
        $rowIds = [125, 126, 127];

        $this->earnRuleProductIndexerResourceMock->expects($this->once())
            ->method('reindexRows')
            ->with($rowIds)
            ->willReturnSelf();

        $this->assertNull($this->indexer->execute($rowIds));
    }

    /**
     * Test execute method if an incorrect id specified
     *
     * @expectedException \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage Bad value was supplied.
     */
    public function testExecuteIncorrectId()
    {
        $rowIds = [];

        $this->earnRuleProductIndexerResourceMock->expects($this->never())
            ->method('reindexRows');
        $this->expectException(InputException::class);
        $this->indexer->execute($rowIds);
    }

    /**
     * Test execute method if an error occurs
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Error!
     */
    public function testExecuteError()
    {
        $rowIds = [125, 126, 127];
        $errorMessage = 'Error!';

        $this->earnRuleProductIndexerResourceMock->expects($this->once())
            ->method('reindexRows')
            ->with($rowIds)
            ->willThrowException(new \Exception($errorMessage));
$this->expectException(\Exception::class);
        $this->indexer->execute($rowIds);
    }
}
