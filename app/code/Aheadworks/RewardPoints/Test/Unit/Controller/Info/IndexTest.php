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
namespace Aheadworks\RewardPoints\Test\Unit\Controller\Info;

use Aheadworks\RewardPoints\Controller\Info\Index;
use Aheadworks\RewardPoints\Model\Config as RewardPointsConfig;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\Http as RequestHttp;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Title;
use Magento\Framework\View\Result\Page;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class Aheadworks\RewardPoints\Test\Unit\Controller\Info\Index$IndexTest
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class IndexTest extends TestCase
{
    /**
     * @var \Magento\Cms\Controller\Index\Index
     */
    private $object;

    /**
     * @var \Magento\Backend\App\Action\Context
     */
    private $context;

    /**
     * @var \Magento\Framework\App\Request|MockObject
     */
    private $requestMock;

    /**
     * @var \Magento\Backend\Model\Session|MockObject
     */
    private $sessionMock;

    /**
     * @var RewardPointsConfig|MockObject
     */
    private $configMock;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerMock;

    /**
     * @var Page|MockObject
     */
    private $resultPageMock;

    /**
     * @var ResultFactory|MockObject
     */
    private $resultFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->sessionMock = $this->getMockBuilder(Session::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestMock = $this->getMockBuilder(RequestHttp::class)
            ->disableOriginalConstructor()->getMock();

        $this->resultFactoryMock = $this->getMockBuilder(ResultFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->configMock = $this->getMockBuilder(RewardPointsConfig::class)
            ->disableOriginalConstructor()
            ->setMethods(['getTabLabelNameRewardPoints'])
            ->getMock();
        $this->storeManagerMock = $this->getMockBuilder(StoreManagerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getWebsite'])
            ->getMockForAbstractClass();

        $this->resultPageMock = $this->getMockBuilder(Page::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getConfig',
                    'getLayout'
                ]
            )
            ->getMock();

        $this->context = $objectManager->getObject(
            Context::class,
            [
                'request' => $this->requestMock,
                'session' => $this->sessionMock,
                'resultFactory' => $this->resultFactoryMock,
            ]
        );

        $this->object = $objectManager->getObject(
            Index::class,
            [
                'context' => $this->context,
                'session' => $this->sessionMock,
                'config' => $this->configMock,
                'storeManager' => $this->storeManagerMock
            ]
        );
    }

    /**
     * @covers Aheadworks\RewardPoints\Controller\Info\Index::execute
     */
    public function testExecute()
    {
        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->resultPageMock);

        $layoutMock = $this->getMockForAbstractClass(LayoutInterface::class);

        $blockMock = $this->getMockBuilder(AbstractBlock::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'setActive',
                    'setRefererUrl'
                ]
            )
            ->getMock();

        $layoutMock->expects($this->exactly(2))
            ->method('getBlock')
            ->willReturn($blockMock);
        $storeMock = $this->getMockBuilder(StoreInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMockForAbstractClass();
        $this->storeManagerMock->expects($this->once())
            ->method('getWebsite')
            ->willReturn($storeMock);
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn(1);
        $this->configMock->expects($this->once())
            ->method('getTabLabelNameRewardPoints')
            ->willReturn('Reward Points');

        $configMock = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->setMethods(['getTitle'])
            ->getMock();

        $this->resultPageMock->expects($this->once())
            ->method('getConfig')
            ->willReturn($configMock);

        $titleMock = $this->getMockBuilder(Title::class)
            ->disableOriginalConstructor()
            ->setMethods(['set'])
            ->getMock();

        $configMock->expects($this->once())
            ->method('getTitle')
            ->willReturn($titleMock);

        $this->resultPageMock->expects($this->exactly(2))
            ->method('getLayout')
            ->willReturn($layoutMock);

        $this->assertInstanceOf(
            Page::class,
            $this->object->execute()
        );
    }
}
