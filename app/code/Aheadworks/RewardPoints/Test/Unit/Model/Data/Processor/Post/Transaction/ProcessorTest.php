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
namespace Aheadworks\RewardPoints\Test\Unit\Model\Data\Processor\Post\Transaction;

use Aheadworks\RewardPoints\Model\Data\Filter\Transaction\CustomerSelection as CustomerSelectionsFilter;
use Aheadworks\RewardPoints\Model\Data\Filter\Transaction\FilterInterface;
use Aheadworks\RewardPoints\Model\Data\Processor\Post\Transaction\Processor;
use Aheadworks\RewardPoints\Model\Data\ProcessorInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class ProcessorTest
 *
 * @package Aheadworks\RewardPoints\Test\Unit\Model\Data\Processor\Post\Transaction
 */
class ProcessorTest extends TestCase
{
    /**
     * @var Processor
     */
    private $object;

    /**
     * @var CustomerSelectionsFilter|MockObject
     */
    private $customerFilterMock;

    /**
     * @var ProcessorInterface|MockObject
     */
    private $processorMock;

    /**
     * @var FilterInterface|MockObject
     */
    private $filterMock;

    /**
     * @var ManagerInterface|MockObject
     */
    private $messageManagerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->customerFilterMock = $this->getMockBuilder(CustomerSelectionsFilter::class)
            ->disableOriginalConstructor()
            ->setMethods(['filter'])
            ->getMockForAbstractClass();
        $this->processorMock = $this->getMockForAbstractClass(ProcessorInterface::class);
        $this->filterMock = $this->getMockForAbstractClass(FilterInterface::class);
        $this->messageManagerMock = $this->getMockBuilder(ManagerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['addSuccessMessage'])
            ->getMockForAbstractClass();
        $data = [
            'messageManager' => $this->messageManagerMock,
            'customerFilter' => $this->customerFilterMock,
            'processors' => [$this->processorMock],
            'filters' => [$this->filterMock]
        ];

        $this->object = $objectManager->getObject(Processor::class, $data);
    }

    /**
     * Test filter method with 'comment_to_customer' and 'comment_to_admin' params
     */
    public function testFilterMethodForCommentFields()
    {
        $testData = [
            'comment_to_customer' => 'comment to customer',
            'comment_to_admin' => 'comment to admin',
        ];
        $result = null;

        $this->filterMock->expects($this->once())
            ->method('filter')
            ->with($testData)
            ->willReturn($testData);
        $this->customerFilterMock->expects($this->once())
            ->method('filter')
            ->with($testData)
            ->willReturn($result);

        $this->assertEquals($result, $this->object->filter($testData));
    }

    /**
     * Test filter method for expiration date
     */
    public function testProcess()
    {
        $testData = [
            'expiration_date' => '09/01/2016',
            'balance' => 10
        ];

        $this->processorMock->expects($this->once())
            ->method('process')
            ->with($testData)
            ->willReturn($testData);

        $this->assertEquals($testData, $this->object->process($testData));
    }

    /**
     * Test customerSelectionFilter method
     */
    public function testCustomerSelectionFilterMethod()
    {
        $testData = [
            'customer_selection' => [1],
        ];

        $this->customerFilterMock->expects($this->once())
            ->method('filter')
            ->with($testData)
            ->willReturn($testData);

        $this->assertEquals($testData, $this->object->customerSelectionFilter($testData));
    }
}
