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
namespace Aheadworks\RewardPoints\Test\Unit\Controller\Adminhtml\Earning\Rules;

use Aheadworks\RewardPoints\Controller\Adminhtml\Earning\Rules\Index;
use Magento\Backend\App\Action\Context;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Title;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Controller\Adminhtml\Earning\Rules\Index
 */
class IndexTest extends TestCase
{
    /**
     * @var Index
     */
    private $controller;

    /**
     * @var Context|MockObject
     */
    private $contextMock;

    /**
     * @var PageFactory|MockObject
     */
    private $resultPageFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->contextMock = $objectManager->getObject(Context::class, []);
        $this->resultPageFactoryMock = $this->createMock(PageFactory::class);

        $this->controller = $objectManager->getObject(
            Index::class,
            [
                'context' => $this->contextMock,
                'resultPageFactory' => $this->resultPageFactoryMock,
            ]
        );
    }

    /**
     * Test execute method
     */
    public function testExecute()
    {
        $titleMock = $this->createMock(Title::class);

        $pageConfigMock = $this->createMock(Config::class);
        $pageConfigMock->expects($this->once())
            ->method('getTitle')
            ->willReturn($titleMock);

        $resultPageMock = $this->getMockBuilder(Page::class)
                               ->disableOriginalCOnstructor()
                               ->setMethods(['setActiveMenu', 'getConfig', 'addBreadcrumb'])
                               ->getMock();
        $resultPageMock->expects($this->once())
            ->method('setActiveMenu')
            ->willReturnSelf();
        $resultPageMock->expects($this->once())
            ->method('getConfig')
            ->willReturn($pageConfigMock);
        $this->resultPageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultPageMock);

        $this->assertSame($resultPageMock, $this->controller->execute());
    }

    /**
     * Test ADMIN_RESOURCE attribute
     */
    public function testAdminResourceAttribute()
    {
        $this->assertEquals(
            'Aheadworks_RewardPoints::aw_reward_points_earning_rules',
            Index::ADMIN_RESOURCE
        );
    }
}
