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
namespace Aheadworks\RewardPoints\Test\Unit\Block\Customer\RewardPointsBalance\Account;

use Aheadworks\RewardPoints\Api\Data\TransactionInterface;
use Aheadworks\RewardPoints\Api\TransactionRepositoryInterface;
use Aheadworks\RewardPoints\Block\Customer\RewardPointsBalance\Account\Transaction;
use Aheadworks\RewardPoints\Block\Html\Pager;
use Aheadworks\RewardPoints\Model\Source\Transaction\Type;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\LayoutInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class Aheadworks\RewardPoints\Test\Unit\Block\Customer\RewardPointsBalance\Account\TransactionTest
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TransactionTest extends TestCase
{
    /**
     * @var Transaction
     */
    private $object;

    /**
     * @var Context|MockObject
     */
    private $contextMock;

    /**
     * @var CustomerSession|MockObject
     */
    private $customerSessionMock;

    /**
     * @var TransactionRepositoryInterface|MockObject
     */
    private $transactionRepositoryMock;

    /**
     * @var SearchCriteriaBuilder|MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var SortOrderBuilder|MockObject
     */
    private $sortOrderBuilderMock;

    /**
     * @var SortOrder|MockObject
     */
    private $sortOrderMock;

    /**
     * @var SearchCriteriaInterface|MockObject
     */
    private $searchCriteriaInterfaceMock;

    /**
     * @var Pager|MockObject
     */
    private $pagerMock;

    /**
     * @var LayoutInterface|MockObject
     */
    private $layoutMock;

    /**
     * @var SearchResultsInterface|MockObject
     */
    private $searchResultsMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->layoutMock = $this->getMockBuilder(LayoutInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['createBlock', 'getChildName', 'renderElement', 'setChild'])
            ->getMockForAbstractClass();

        $this->contextMock = $objectManager->getObject(
            Context::class,
            ['layout' => $this->layoutMock]
        );

        $this->customerSessionMock = $this->getMockBuilder(CustomerSession::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCustomerId'])
            ->getMock();

        $this->searchCriteriaBuilderMock = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['setCurrentPage', 'setPageSize', 'addFilter', 'create'])
            ->getMock();

        $this->sortOrderBuilderMock = $this->getMockBuilder(SortOrderBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['setField', 'setDescendingDirection', 'create'])
            ->getMock();

        $this->sortOrderMock = $this->getMockBuilder(SortOrder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->pagerMock = $this->getMockBuilder(Pager::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCurrentPage', 'getLimit', 'setSearchResults', 'create'])
            ->getMock();

        $this->searchCriteriaInterfaceMock = $this->getMockBuilder(SearchCriteriaInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->searchResultsMock = $this->getMockBuilder(SearchResultsInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->transactionRepositoryMock = $this->getMockBuilder(TransactionRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getList'])
            ->getMockForAbstractClass();

        $data = [
            'context' => $this->contextMock,
            'customerSession' => $this->customerSessionMock,
            'transactionRepository' => $this->transactionRepositoryMock,
            'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
            'sortOrderBuilder' => $this->sortOrderBuilderMock
        ];

        $this->object = $objectManager->getObject(Transaction::class, $data);
    }

    /**
     * Test getTransactions method
     */
    public function testGetTransactions()
    {
        $expectedValue = [3, 4, 5];
        $customerId = 5;

        $this->customerSessionMock->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $this->transactionRepositoryMock->expects($this->once())
            ->method('getList')
            ->willReturn($expectedValue);

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($this->searchCriteriaInterfaceMock);

        $this->sortOrderBuilderMock->expects($this->once())
            ->method('setField')
            ->willReturnSelf();

        $this->sortOrderBuilderMock->expects($this->once())
            ->method('setDescendingDirection')
            ->willReturnSelf();

        $this->sortOrderBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($this->sortOrderMock);

        $this->assertEquals($expectedValue, $this->object->getTransactions());
    }

    /**
     * Test renderComment method
     */
    public function testRenderCommentMethod()
    {
        $expectedValue = 'Comment';
        $transactionType = Type::BALANCE_ADJUSTED_BY_ADMIN;

        $transactionMock = $this->getMockForAbstractClass(TransactionInterface::class);
        $transactionMock->expects($this->once())
            ->method('getType')
            ->willReturn($transactionType);
        $transactionMock->expects($this->once())
            ->method('getCommentToCustomer')
            ->willReturn($expectedValue);

        $this->assertEquals($expectedValue, $this->object->renderComment($transactionMock));
    }

    /**
     * Test getPagerHtml method
     */
    public function testGetPagerHtmlMethod()
    {
        $expectedValue = '<div class="pager"></div>';
        $customerId = 5;

        $this->customerSessionMock->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($this->searchCriteriaInterfaceMock);

        $this->sortOrderBuilderMock->expects($this->once())
            ->method('setField')
            ->willReturnSelf();
        $this->sortOrderBuilderMock->expects($this->once())
            ->method('setDescendingDirection')
            ->willReturnSelf();
        $this->sortOrderBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($this->sortOrderMock);

        $this->transactionRepositoryMock->expects($this->once())
            ->method('getList')
            ->willReturn($this->searchResultsMock);

        $this->pagerMock->expects($this->once())
            ->method('getCurrentPage')
            ->willReturn(1);
        $this->pagerMock->expects($this->once())
            ->method('getLimit')
            ->willReturn(10);

        $this->layoutMock->expects($this->once())
            ->method('createBlock')
            ->with(Pager::class, 'aw_rp_transaction.pager')
            ->willReturn($this->pagerMock);

        $this->layoutMock->expects($this->any())
            ->method('getChildName')
            ->willReturn('pager');

        $this->layoutMock->expects($this->once())
            ->method('renderElement')
            ->willReturn($expectedValue);

        $class = new \ReflectionClass(Transaction::class);
        $methodGetAvailableAmount = $class->getMethod('_prepareLayout');
        $methodGetAvailableAmount->setAccessible(true);
        $methodGetAvailableAmount->invoke($this->object);

        $this->assertEquals($expectedValue, $this->object->getPagerHtml());
    }
}
