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

use Aheadworks\RewardPoints\Api\Data\EarnRuleInterface;
use Aheadworks\RewardPoints\Api\EarnRuleRepositoryInterface;
use Aheadworks\RewardPoints\Controller\Adminhtml\Earning\Rules\Edit;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Title;
use Magento\Framework\View\Result\PageFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Controller\Adminhtml\Earning\Rules\Edit
 */
class EditTest extends TestCase
{
    /**
     * @var Edit
     */
    private $controller;

    /**
     * @var Context|MockObject
     */
    private $contextMock;

    /**
     * @var Http|MockObject
     */
    private $requestMock;

    /**
     * @var RedirectFactory|MockObject
     */
    private $resultRedirectFactoryMock;

    /**
     * @var ManagerInterface|MockObject
     */
    private $messageManagerMock;

    /**
     * @var RuleRepositoryInterface|MockObject
     */
    private $ruleRepositoryMock;

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

        $this->requestMock = $this->createMock(Http::class);
        $this->resultRedirectFactoryMock = $this->createMock(RedirectFactory::class);
        $this->messageManagerMock = $this->createMock(ManagerInterface::class);

        $this->contextMock = $objectManager->getObject(
            Context::class,
            [
                'request' => $this->requestMock,
                'messageManager' => $this->messageManagerMock,
                'resultRedirectFactory' => $this->resultRedirectFactoryMock,
            ]
        );

        $this->ruleRepositoryMock = $this->createMock(EarnRuleRepositoryInterface::class);
        $this->resultPageFactoryMock = $this->createMock(PageFactory::class);

        $this->controller = $objectManager->getObject(
            Edit::class,
            [
                'context' => $this->contextMock,
                'ruleRepository' => $this->ruleRepositoryMock,
                'resultPageFactory' => $this->resultPageFactoryMock,
            ]
        );
    }

    /**
     * Test execute (edit rule)
     */
    public function testExecuteEditRule()
    {
        $ruleId = 10;
        $ruleName = 'Sample Rule';

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($ruleId);

        $ruleMock = $this->createMock(EarnRuleInterface::class);
        $ruleMock->expects($this->once())
            ->method('getName')
            ->willReturn($ruleName);
        $this->ruleRepositoryMock->expects($this->once())
            ->method('get')
            ->with($ruleId)
            ->willReturn($ruleMock);

        $titleMock = $this->createMock(Title::class);
        $titleMock->expects($this->once())
            ->method('prepend')
            ->with(__('Edit "%1" Rule', $ruleName))
            ->willReturn(null);

        $pageConfigMock = $this->createMock(Config::class);
        $pageConfigMock->expects($this->once())
            ->method('getTitle')
            ->willReturn($titleMock);

        $resultPageMock = $this->createMock(Page::class);
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
     * Test execute (edit rule) if no rule found
     */
    public function testExecuteEditNoRule()
    {
        $ruleId = 10;
        $exception = new NoSuchEntityException(__('No such entity!'));

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($ruleId);

        $this->ruleRepositoryMock->expects($this->once())
            ->method('get')
            ->with($ruleId)
            ->willThrowException($exception);

        $this->messageManagerMock->expects($this->once())
            ->method('addExceptionMessage')
            ->with($exception, __('The rule #%1 not found.', $ruleId))
            ->willReturnSelf();

        $redirectMock = $this->createMock(Redirect::class);
        $redirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();
        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($redirectMock);

        $this->assertSame($redirectMock, $this->controller->execute());
    }

    /**
     * Test execute (new rule)
     */
    public function testExecuteNewRule()
    {
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn(null);

        $this->ruleRepositoryMock->expects($this->never())
            ->method('get');

        $titleMock = $this->createMock(Title::class);
        $titleMock->expects($this->once())
            ->method('prepend')
            ->with(__('New Rule'))
            ->willReturn(null);

        $pageConfigMock = $this->createMock(Config::class);
        $pageConfigMock->expects($this->once())
            ->method('getTitle')
            ->willReturn($titleMock);

        $resultPageMock = $this->createMock(Page::class);
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
}
