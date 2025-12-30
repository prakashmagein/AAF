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

use Aheadworks\RewardPoints\Model\ResourceModel\Order;
use Magento\CatalogInventory\Model\Stock;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\TransactionManager;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Sales\Model\Order as SalesOrder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class Aheadworks\RewardPoints\Test\Unit\Model\ResourceModel\OrderTest
 */
class OrderTest extends TestCase
{
    /**
     * @var Order
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
     * @var Select|MockObject
     */
    private $selectMock;

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

        $this->selectMock = $this->getMockBuilder(Select::class)
            ->setMethods(
                [
                    'joinInner',
                    'where',
                    'from',
                ]
            )
            ->disableOriginalConstructor()
            ->getMock();

        $this->connectionMock = $this->getMockBuilder(Mysql::class)
            ->setMethods(
                [
                    'select',
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

        $this->object = $objectManager->getObject(Order::class, $data);
    }

    /**
     * Test getCustomersOrdersByProductId
     */
    public function testIsCustomersOwnerOfProductId()
    {
        $customerId = 2;
        $productId = 1049;

        $this->resourcesMock->expects($this->exactly(1))
            ->method('getConnection')
            ->with('sales')
            ->willReturn($this->connectionMock);

        $this->resourcesMock->expects($this->exactly(2))
            ->method('getTableName')
            ->withConsecutive(['sales_order'], ['sales_order_item'])
            ->willReturnOnConsecutiveCalls('sales_order', 'sales_order_item');

        $this->connectionMock->expects($this->once())
            ->method('select')
            ->willReturn($this->selectMock);

        $this->selectMock->expects($this->once())
            ->method('from')
            ->with('sales_order', 'entity_id')
            ->willReturnSelf();

        $this->selectMock->expects($this->once())
            ->method('joinInner')
            ->with(
                ['items' => 'sales_order_item'],
                'entity_id = items.order_id AND items.product_id = ' . $productId,
                ['product_id' => 'product_id']
            )
            ->willReturnSelf();

        $this->selectMock->expects($this->exactly(2))
            ->method('where')
            ->withConsecutive(
                ['customer_id = '. $customerId],
                ['sales_order.state IN (?)', [SalesOrder::STATE_COMPLETE]]
            )
            ->willReturnSelf();

        $this->connectionMock->expects($this->once())
            ->method('fetchOne')
            ->with($this->selectMock)
            ->willReturn(null);

        $expectedValue = false;
        $actualValue = $this->object->isCustomersOwnerOfProductId($customerId, $productId);

        $this->assertEquals($expectedValue, $actualValue);
    }
}
