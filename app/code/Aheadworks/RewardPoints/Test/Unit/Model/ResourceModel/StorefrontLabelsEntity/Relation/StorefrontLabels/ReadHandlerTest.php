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
namespace Aheadworks\RewardPoints\Test\Unit\Model\ResourceModel\StorefrontLabelsEntity\Relation\StorefrontLabels;

use Aheadworks\RewardPoints\Api\Data\StorefrontLabelsEntityInterface;
use Aheadworks\RewardPoints\Api\Data\StorefrontLabelsInterface;
use Aheadworks\RewardPoints\Model\ResourceModel\StorefrontLabels\Repository;
use Aheadworks\RewardPoints\Model\ResourceModel\StorefrontLabelsEntity\Relation\StorefrontLabels\ReadHandler;
use Aheadworks\RewardPoints\Model\StorefrontLabelsResolver;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Model\ResourceModel\StorefrontLabelsEntity\Relation\StorefrontLabels\ReadHandler
 */
class ReadHandlerTest extends TestCase
{
    /**
     * @var ReadHandler
     */
    private $readHandler;

    /**
     * @var Repository|MockObject
     */
    private $repositoryMock;

    /**
     * @var StorefrontLabelsResolver|MockObject
     */
    private $storefrontLabelsResolverMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->repositoryMock = $this->createMock(
            Repository::class
        );
        $this->storefrontLabelsResolverMock = $this->createMock(
            StorefrontLabelsResolver::class
        );

        $this->readHandler = $objectManager->getObject(
            ReadHandler::class,
            [
                'repository' => $this->repositoryMock,
                'storefrontLabelsResolver' => $this->storefrontLabelsResolverMock,
            ]
        );
    }

    /**
     * Test for execute() method
     */
    public function testExecuteSuccessful()
    {
        $entityId = 12;
        $storeId = 2;
        $arguments = [
            'store_id' => $storeId
        ];

        $labelObject = $this->createMock(StorefrontLabelsInterface::class);
        $labelsObjects = [$labelObject];
        $currentLabelsRecord = $labelObject;

        $entity = $this->createMock(StorefrontLabelsEntityInterface::class);
        $entity->expects($this->once())
            ->method('getEntityId')
            ->willReturn($entityId);
        $entity->expects($this->once())
            ->method('setLabels')
            ->with($labelsObjects)
            ->willReturnSelf();
        $entity->expects($this->once())
            ->method('setCurrentLabels')
            ->with($currentLabelsRecord)
            ->willReturnSelf();

        $this->repositoryMock->expects($this->once())
            ->method('get')
            ->with($entity)
            ->willReturn($labelsObjects);
        $this->storefrontLabelsResolverMock->expects($this->once())
            ->method('getLabelsForStore')
            ->with($labelsObjects, $storeId)
            ->willReturn($currentLabelsRecord);

        $this->assertSame($entity, $this->readHandler->execute($entity, $arguments));
    }

    /**
     * Test for execute() method
     */
    public function testExecuteSuccessfulNoStore()
    {
        $entityId = 12;
        $storeId = null;
        $arguments = [];

        $labelObject = $this->createMock(StorefrontLabelsInterface::class);
        $labelsObjects = [$labelObject];
        $currentLabelsRecord = $labelObject;

        $entity = $this->createMock(StorefrontLabelsEntityInterface::class);
        $entity->expects($this->once())
            ->method('getEntityId')
            ->willReturn($entityId);
        $entity->expects($this->once())
            ->method('setLabels')
            ->with($labelsObjects)
            ->willReturnSelf();
        $entity->expects($this->once())
            ->method('setCurrentLabels')
            ->with($currentLabelsRecord)
            ->willReturnSelf();

        $this->repositoryMock->expects($this->once())
            ->method('get')
            ->with($entity)
            ->willReturn($labelsObjects);
        $this->storefrontLabelsResolverMock->expects($this->once())
            ->method('getLabelsForStore')
            ->with($labelsObjects, $storeId)
            ->willReturn($currentLabelsRecord);

        $this->assertSame($entity, $this->readHandler->execute($entity, $arguments));
    }

    /**
     * Test for execute() method
     */
    public function testExecuteNewEntity()
    {
        $entityId = null;
        $storeId = 2;
        $arguments = [
            'store_id' => $storeId
        ];

        $entity = $this->createMock(StorefrontLabelsEntityInterface::class);
        $entity->expects($this->once())
            ->method('getEntityId')
            ->willReturn($entityId);
        $entity->expects($this->never())
            ->method('setLabels');
        $entity->expects($this->never())
            ->method('setCurrentLabels');

        $this->repositoryMock->expects($this->never())
            ->method('get')
            ->with($entity);
        $this->storefrontLabelsResolverMock->expects($this->never())
            ->method('getLabelsForStore');

        $this->assertSame($entity, $this->readHandler->execute($entity, $arguments));
    }

    /**
     * Test for execute() method
     */
    public function testExecuteNewEntityNoStore()
    {
        $entityId = null;
        $arguments = [];

        $entity = $this->createMock(StorefrontLabelsEntityInterface::class);
        $entity->expects($this->once())
            ->method('getEntityId')
            ->willReturn($entityId);
        $entity->expects($this->never())
            ->method('setLabels');
        $entity->expects($this->never())
            ->method('setCurrentLabels');

        $this->repositoryMock->expects($this->never())
            ->method('get')
            ->with($entity);
        $this->storefrontLabelsResolverMock->expects($this->never())
            ->method('getLabelsForStore');

        $this->assertSame($entity, $this->readHandler->execute($entity, $arguments));
    }

    /**
     * Test for execute() method
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Error!
     */
    public function testExecuteException()
    {
        $entityId = 13;
        $storeId = 2;
        $arguments = [
            'store_id' => $storeId
        ];

        $entity = $this->createMock(StorefrontLabelsEntityInterface::class);
        $entity->expects($this->once())
            ->method('getEntityId')
            ->willReturn($entityId);
        $entity->expects($this->never())
            ->method('setLabels');
        $entity->expects($this->never())
            ->method('setCurrentLabels');

        $this->repositoryMock->expects($this->once())
            ->method('get')
            ->with($entity)
            ->willThrowException(new \Exception("Error!"));
        $this->expectException(\Exception::class);
        $this->storefrontLabelsResolverMock->expects($this->never())
            ->method('getLabelsForStore');

        $this->readHandler->execute($entity, $arguments);
    }

    /**
     * Test for execute() method
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Error!
     */
    public function testExecuteNoStoreException()
    {
        $entityId = 13;
        $arguments = [];

        $entity = $this->createMock(StorefrontLabelsEntityInterface::class);
        $entity->expects($this->once())
            ->method('getEntityId')
            ->willReturn($entityId);
        $entity->expects($this->never())
            ->method('setLabels');
        $entity->expects($this->never())
            ->method('setCurrentLabels');

        $this->repositoryMock->expects($this->once())
            ->method('get')
            ->with($entity)
            ->willThrowException(new \Exception("Error!"));
        $this->expectException(\Exception::class);
        $this->storefrontLabelsResolverMock->expects($this->never())
            ->method('getLabelsForStore');

        $this->readHandler->execute($entity, $arguments);
    }
}
