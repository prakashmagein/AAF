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
namespace Aheadworks\RewardPoints\Test\Unit\Model\Import;

use Aheadworks\RewardPoints\Model\Import\Exception\ImportValidatorException;
use Aheadworks\RewardPoints\Model\Import\Logger;
use Aheadworks\RewardPoints\Model\Import\PointsSummary as ImportPointsSummary;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerSearchResultsInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\MassAction\Filter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class PointsSummaryTest
 * Test for \Aheadworks\RewardPoints\Model\Import\PointsSummary
 *
 * @package Aheadworks\RewardPoints\Test\Unit\Model\Import
 */
class PointsSummaryTest extends TestCase
{
    /**
     * @var ImportPointsSummary|MockObject
     */
    private $model;

    /**
     * @var DataObjectHelper|MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var Filter|MockObject
     */
    private $filterMock;

    /**
     * @var RequestInterface|MockObject
     */
    private $requestMock;

    /**
     * @var UrlInterface|MockObject
     */
    private $urlMock;

    /**
     * @var SearchCriteriaBuilder|MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var CustomerRepositoryInterface|MockObject
     */
    private $customerRepositoryMock;

    /**
     * @var Logger|MockObject
     */
    private $loggerMock;

    /**
     * @var DirectoryList|MockObject
     */
    private $directoryList;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->filterMock = $this->getMockBuilder(Filter::class)
            ->setMethods(['create', 'getComponent', 'prepareComponent'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);
        $this->urlMock = $this->getMockBuilder(UrlInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUrl'])
            ->getMockForAbstractClass();
        $this->searchCriteriaBuilderMock = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->setMethods(['create', 'addFilter'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->customerRepositoryMock = $this->getMockBuilder(CustomerRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getList', 'getItems'])
            ->getMockForAbstractClass();
        $this->loggerMock = $this->getMockBuilder(Logger::class)
            ->setMethods(['init', 'addMessage'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->directoryList = $this->getMockBuilder(DirectoryList::class)
            ->setMethods(['getRoot'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            ImportPointsSummary::class,
            [
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'filter' => $this->filterMock,
                'request' => $this->requestMock,
                'url' => $this->urlMock,
                'logger' => $this->loggerMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
                'customerRepository' => $this->customerRepositoryMock,
                'directoryList' => $this->directoryList,
            ]
        );
    }

    /**
     * Testing of getUrlToLogFile method
     */
    public function testGetUrlToLogFile()
    {
        $baseWebUrl = 'https://ecommerce.aheadworks.com/';

        $this->urlMock->expects($this->once())
            ->method('getBaseUrl')
            ->with(['_type' => UrlInterface::URL_TYPE_WEB])
            ->willReturn($baseWebUrl);

        $importPointsSummaryClass = new \ReflectionClass(ImportPointsSummary::class);
        $methodGetPathToLogFile = $importPointsSummaryClass->getMethod('getPathToLogFile');
        $methodGetPathToLogFile->setAccessible(true);
        $pathToLogFile = $methodGetPathToLogFile->invoke($this->model);

        $urlToLogFile = $baseWebUrl . $pathToLogFile;

        $this->assertEquals($urlToLogFile, $this->model->getUrlToLogFile());
    }

    /**
     * Testing of getMessages method
     */
    public function testGetMessages()
    {
        $this->assertTrue(is_array($this->model->getMessages()));
    }

    /**
     * Testing of process method throw exception
     *
     * @expectedException \Aheadworks\RewardPoints\Model\Import\Exception\ImportValidatorException
     */
    public function testProcessThrowException()
    {
        $rawData = [[]];
        $uiComponent = $this->getMockForAbstractClass(UiComponentInterface::class);

        $this->filterMock->expects($this->once())
            ->method('getComponent')
            ->willReturn($uiComponent);

        $this->filterMock->expects($this->once())
            ->method('prepareComponent')
            ->willReturn(null);

        $this->loggerMock->expects($this->once())
            ->method('init')
            ->with($this->isType('string'))
            ->willReturnSelf();
        $this->loggerMock->expects($this->atLeastOnce())
            ->method('addMessage')
            ->willReturnSelf();
        $this->expectException(ImportValidatorException::class);
        $this->model->process($rawData);
    }

    /**
     * Testing of process method without messages in the log
     *
     */
    public function testProcessWithoutMessages()
    {
        $rawData = [
            [
                'Customer Email',
                'Current customer balance',
                'Website',
                'Balance Update Notifications (status)',
                'Points Expiration Notification (status)',
            ],
            [
                'awtest201504@gmail.com',
                0,
                'Main Website',
                'Unsubscribed',
                'Subscribed'
            ],
            [
                'awtest201509@gmail.com',
                240,
                'Main Website',
                'Subscribed',
                'Unsubscribed'
            ]
        ];

        $listingTopUiComponent = $this->getMockBuilder(UiComponentInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getChildComponents'])
            ->getMockForAbstractClass();
        $listingTopUiComponent->expects($this->exactly(2))
            ->method('getChildComponents')
            ->willReturn([]);

        $uiComponent = $this->getMockBuilder(UiComponentInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getChildComponents'])
            ->getMockForAbstractClass();
        $uiComponent->expects($this->exactly(2))
            ->method('getChildComponents')
            ->willReturn(['listing_top' => $listingTopUiComponent]);

        $this->filterMock->expects($this->exactly(3))
            ->method('getComponent')
            ->willReturn($uiComponent);
        $this->filterMock->expects($this->once())
            ->method('prepareComponent')
            ->willReturn(null);

        $this->searchCriteriaBuilderMock->expects($this->exactly(4))
            ->method('addFilter')
            ->willReturnSelf();
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $this->searchCriteriaBuilderMock->expects($this->exactly(2))
            ->method('create')
            ->willReturn($searchCriteriaMock);
        $searchResultMock = $this->getMockForAbstractClass(CustomerSearchResultsInterface::class);
        $this->customerRepositoryMock->expects($this->exactly(2))
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($searchResultMock);

        $customer = $this->getMockBuilder(CustomerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMockForAbstractClass();
        $customer->expects($this->exactly(2))
            ->method('getId')
            ->willReturn('1');
        $customers = [$customer];
        $searchResultMock->expects($this->exactly(2))
            ->method('getItems')
            ->willReturn($customers);

        $this->loggerMock->expects($this->once())
            ->method('init')
            ->with($this->isType('string'))
            ->willReturnSelf();
        $this->loggerMock->expects($this->never())
            ->method('addMessage')
            ->willReturnSelf();

        $this->model->process($rawData);
        $this->assertTrue(empty($this->model->getMessages()));
    }

    /**
     * Testing of process method with messages in the log
     *
     */
    public function testProcessWithMessages()
    {
        $rawData = [
            [
                'Customer Email',
                'Current customer balance',
                'Website',
                'Balance Update Notifications (status)',
                'Points Expiration Notification (status)',
            ],
            [
                'awtest201504@gmail.com',
                0,
                'Main Website',
                'Unsubscribed',
                'Subscribed'
            ],
            [
                240,
                'Main Website',
                'Subscribed',
                'Unsubscribed'
            ]
        ];

        $listingTopUiComponent = $this->getMockBuilder(UiComponentInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getChildComponents'])
            ->getMockForAbstractClass();
        $listingTopUiComponent->expects($this->exactly(2))
            ->method('getChildComponents')
            ->willReturn([]);

        $uiComponent = $this->getMockBuilder(UiComponentInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getChildComponents'])
            ->getMockForAbstractClass();
        $uiComponent->expects($this->exactly(2))
            ->method('getChildComponents')
            ->willReturn(['listing_top' => $listingTopUiComponent]);

        $this->filterMock->expects($this->exactly(3))
            ->method('getComponent')
            ->willReturn($uiComponent);
        $this->filterMock->expects($this->once())
            ->method('prepareComponent')
            ->willReturn(null);

        $this->searchCriteriaBuilderMock->expects($this->exactly(4))
            ->method('addFilter')
            ->willReturnSelf();
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $this->searchCriteriaBuilderMock->expects($this->exactly(2))
            ->method('create')
            ->willReturn($searchCriteriaMock);
        $searchResultMock = $this->getMockForAbstractClass(CustomerSearchResultsInterface::class);
        $this->customerRepositoryMock->expects($this->exactly(2))
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($searchResultMock);

        $customer = $this->getMockBuilder(CustomerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMockForAbstractClass();
        $customer->expects($this->exactly(2))
            ->method('getId')
            ->willReturn('1');
        $customers = [$customer];
        $searchResultMock->expects($this->exactly(2))
            ->method('getItems')
            ->willReturn($customers);

        $this->loggerMock->expects($this->once())
            ->method('init')
            ->with($this->isType('string'))
            ->willReturnSelf();
        $this->loggerMock->expects($this->atLeastOnce())
            ->method('addMessage')
            ->willReturnSelf();

        $this->model->process($rawData);
        $this->assertTrue(count($this->model->getMessages()) > 0);
    }
}
