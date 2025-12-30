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
namespace Aheadworks\RewardPoints\Test\Unit\Controller\Adminhtml\Customers;

use Aheadworks\RewardPoints\Controller\Adminhtml\Customers\Index as CustomersIndexController;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Title;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class Aheadworks\RewardPoints\Test\Unit\Controller\Adminhtml\Customers\IndexTest
 */
class IndexTest extends TestCase
{
    /**
     * @var CustomersIndexController
     */
    private $object;

    /**
     * @var PageFactory|MockObject
     */
    private $resultPageFactoryMock;

    /**
     * @var Page|MockObject
     */
    private $resultPageMock;

    /**
     * @var Config|MockObject
     */
    private $pageConfigMock;

    /**
     * @var Title|MockObject
     */
    private $pageTitleMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->resultPageFactoryMock = $this->getMockBuilder(PageFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultPageMock = $this->getMockBuilder(Page::class)
            ->disableOriginalConstructor()
            ->setMethods(['setActiveMenu', 'getConfig', 'addBreadcrumb'])
            ->getMock();
        $this->pageConfigMock = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->pageTitleMock = $this->getMockBuilder(Title::class)
            ->disableOriginalConstructor()
            ->getMock();

        $data = [
            'resultPageFactory' => $this->resultPageFactoryMock,
        ];

        $this->object = $objectManager->getObject(CustomersIndexController::class, $data);
    }

    /**
     * Test execute method
     */
    public function testExecute()
    {
        $this->resultPageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->resultPageMock);

        $this->resultPageMock->expects($this->once())
            ->method('setActiveMenu')
            ->with('Aheadworks_RewardPoints::aw_reward_points_customers');

        $this->resultPageMock->expects($this->once())
            ->method('getConfig')
            ->willReturn($this->pageConfigMock);

        $this->pageConfigMock->expects($this->once())
            ->method('getTitle')
            ->willReturn($this->pageTitleMock);

        $this->pageTitleMock->expects($this->once())
            ->method('prepend')
            ->with('Customers');

        $this->resultPageMock->expects($this->once())
            ->method('addBreadcrumb')
            ->with('Aheadworks Reward Points', 'Customers');

        $this->assertInstanceOf(
            Page::class,
            $this->object->execute()
        );
    }

    /**
     * Test ADMIN_RESOURCE attribute
     */
    public function testAdminResourceAttribute()
    {
        $this->assertEquals(
            'Aheadworks_RewardPoints::aw_reward_points_customers',
            CustomersIndexController::ADMIN_RESOURCE
        );
    }
}
