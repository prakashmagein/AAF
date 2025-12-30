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

use Aheadworks\RewardPoints\Api\EarnRuleManagementInterface;
use Aheadworks\RewardPoints\Controller\Adminhtml\Earning\Rules\MassDisable;
use Aheadworks\RewardPoints\Model\ResourceModel\EarnRule\Collection as EarnRuleCollection;
use Aheadworks\RewardPoints\Model\ResourceModel\EarnRule\CollectionFactory as EarnRuleCollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Ui\Component\MassAction\Filter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Controller\Adminhtml\Earning\Rules\MassDisable
 */
class MassDisableTest extends TestCase
{
    /**
     * @var MassDisable
     */
    private $controller;

    /**
     * @var Context|MockObject
     */
    private $contextMock;

    /**
     * @var RedirectFactory|MockObject
     */
    private $resultRedirectFactoryMock;

    /**
     * @var ManagerInterface|MockObject
     */
    private $messageManagerMock;

    /**
     * @var Filter|MockObject
     */
    private $filterMock;

    /**
     * @var EarnRuleCollectionFactory|MockObject
     */
    private $ruleCollectionFactoryMock;

    /**
     * @var EarnRuleManagementInterface|MockObject
     */
    private $ruleManagementMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->resultRedirectFactoryMock = $this->createMock(RedirectFactory::class);
        $this->messageManagerMock = $this->createMock(ManagerInterface::class);

        $this->contextMock = $objectManager->getObject(
            Context::class,
            [
                'messageManager' => $this->messageManagerMock,
                'resultRedirectFactory' => $this->resultRedirectFactoryMock,
            ]
        );

        $this->filterMock = $this->createMock(Filter::class);
        $this->ruleCollectionFactoryMock = $this->createMock(EarnRuleCollectionFactory::class);
        $this->ruleManagementMock = $this->createMock(EarnRuleManagementInterface::class);

        $this->controller = $objectManager->getObject(
            MassDisable::class,
            [
                'context' => $this->contextMock,
                'filter' => $this->filterMock,
                'ruleCollectionFactory' => $this->ruleCollectionFactoryMock,
                'ruleManagement' => $this->ruleManagementMock
            ]
        );
    }

    /**
     * Test execute
     */
    public function testExecute()
    {
        $ruleIds = [10, 11];
        $ruleCount = 2;

        $collectionMock = $this->createMock(EarnRuleCollection::class);
        $collectionMock->expects($this->once())
            ->method('getAllIds')
            ->willReturn($ruleIds);
        $this->ruleCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($collectionMock);

        $this->filterMock->expects($this->once())
            ->method('getCollection')
            ->with($collectionMock)
            ->willReturn($collectionMock);

        $this->ruleManagementMock->expects($this->exactly(2))
            ->method('disable')
            ->withConsecutive([10], [11])
            ->willReturn(true);

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('A total of %1 rule(s) were disabled.', $ruleCount))
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
     * Test execute method if no rule found
     */
    public function testExecuteNoRuleFound()
    {
        $ruleIds = [10, 11];
        $errorMessage = 'No such entity!';

        $collectionMock = $this->createMock(EarnRuleCollection::class);
        $collectionMock->expects($this->once())
            ->method('getAllIds')
            ->willReturn($ruleIds);
        $this->ruleCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($collectionMock);

        $this->filterMock->expects($this->once())
            ->method('getCollection')
            ->with($collectionMock)
            ->willReturn($collectionMock);

        $this->ruleManagementMock->expects($this->once())
            ->method('disable')
            ->with(10)
            ->willThrowException(new NoSuchEntityException(__($errorMessage)));

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with($errorMessage)
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
     * Test execute method if rule can not be saved
     */
    public function testExecuteSaveError()
    {
        $ruleIds = [10, 11];
        $errorMessage = 'Rule can not be saved!';

        $collectionMock = $this->createMock(EarnRuleCollection::class);
        $collectionMock->expects($this->once())
            ->method('getAllIds')
            ->willReturn($ruleIds);
        $this->ruleCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($collectionMock);

        $this->filterMock->expects($this->once())
            ->method('getCollection')
            ->with($collectionMock)
            ->willReturn($collectionMock);

        $this->ruleManagementMock->expects($this->once())
            ->method('disable')
            ->with(10)
            ->willThrowException(new CouldNotSaveException(__($errorMessage)));

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with($errorMessage)
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
}
