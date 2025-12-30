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
namespace Aheadworks\RewardPoints\Test\Unit\Model\ResourceModel;

use Aheadworks\RewardPoints\Model\PointsSummary as PointsSummaryModel;
use Aheadworks\RewardPoints\Model\ResourceModel\PointsSummary;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\TransactionManager;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PointsSummaryTest extends TestCase
{
    /**
     * @var PointsSummary
     */
    private $object;

    /**
     * @var Context|MockObject
     */
    private $contextMock;

    /**
     * @var TransactionManager|MockObject
     */
    private $transactionManagerMock;

    /**
     * @var ResourceConnection|MockObject
     */
    private $resourcesMock;

    /**
     * @var Mysql|MockObject
     */
    private $connectionMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->transactionManagerMock = $this->getMockBuilder(TransactionManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resourcesMock = $this->getMockBuilder(ResourceConnection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->connectionMock = $this->getMockBuilder(Mysql::class)
            ->setMethods(
                [
                    'prepareColumnValue',
                    'delete',
                    'describeTable',
                    'insert',
                    'select',
                    'quoteIdentifier',
                    'fetchRow',
                    'fetchOne',
                ]
            )
            ->disableOriginalConstructor()
            ->getMock();

        $this->contextMock->expects($this->once())
            ->method('getTransactionManager')
            ->willReturn($this->transactionManagerMock);
        $this->contextMock->expects($this->once())
            ->method('getResources')
            ->willReturn($this->resourcesMock);

        $data = [
            'context' => $this->contextMock,
        ];

        $this->object = $objectManager->getObject(PointsSummary::class, $data);
    }

    /**
     * Test loadByCustomerId method
     */
    public function testLoadByCustomerIdMethod()
    {
        $customerId = 10;

        $expectedValue = [
            'summary_id' => 5,
            'website_id' => 1,
            'customer_id' => 10,
            'points' => 100,
        ];

        $pointsSummaryModel = $this->getMockBuilder(PointsSummaryModel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resourcesMock->expects($this->exactly(3))
            ->method('getConnection')
            ->with('default')
            ->willReturn($this->connectionMock);

        $this->connectionMock->expects($this->once())
            ->method('quoteIdentifier')
            ->with('aw_rp_points_summary.customer_id')
            ->willReturn('`aw_rp_points_summary`.`customer_id`');

        $selectMock = $this->getMockBuilder(Select::class)
            ->setMethods(['from', 'where', 'order'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->connectionMock->expects($this->once())
            ->method('select')
            ->willReturn($selectMock);

        $this->resourcesMock->expects($this->once())
            ->method('getTableName')
            ->with('aw_rp_points_summary', 'default')
            ->will($this->returnArgument(0));

        $selectMock->expects($this->once())
            ->method('from')
            ->with('aw_rp_points_summary')
            ->willReturnSelf();

        $selectMock->expects($this->once())
            ->method('where')
            ->with('`aw_rp_points_summary`.`customer_id`=?', $customerId)
            ->willReturnSelf();

        $this->connectionMock->expects($this->once())
            ->method('fetchRow')
            ->with($selectMock)
            ->willReturn($expectedValue);

        $pointsSummaryModel->expects($this->once())
            ->method('setData')
            ->with($expectedValue)
            ->willReturnSelf();

        $this->object->loadByCustomerId($pointsSummaryModel, $customerId);
    }

    /**
     * Test getIdByCustomerId method
     */
    public function testGetIdByCustomerIdMethod()
    {
        $this->resourcesMock->expects($this->once())
            ->method('getConnection')
            ->with('default')
            ->willReturn($this->connectionMock);

        $selectMock = $this->getMockBuilder(Select::class)
            ->setMethods(['from', 'where', 'order'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->connectionMock->expects($this->once())
            ->method('select')
            ->willReturn($selectMock);

        $this->resourcesMock->expects($this->once())
            ->method('getTableName')
            ->with('aw_rp_points_summary', 'default')
            ->will($this->returnArgument(0));

        $selectMock->expects($this->once())
            ->method('from')
            ->with('aw_rp_points_summary', 'summary_id')
            ->willReturnSelf();

        $selectMock->expects($this->once())
            ->method('where')
            ->with('customer_id = :customer_id')
            ->willReturnSelf();

        $this->connectionMock->expects($this->once())
            ->method('fetchOne')
            ->with($selectMock, [':customer_id' => 5])
            ->willReturn(1);

        $this->assertEquals(1, $this->object->getIdByCustomerId(5));
    }
}
