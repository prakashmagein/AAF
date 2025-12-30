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
namespace Aheadworks\RewardPoints\Test\Unit\Model\StorefrontLabelsEntity\Store;

use Aheadworks\RewardPoints\Model\StorefrontLabelsEntity\Store\Resolver;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Model\StorefrontLabelsEntity\Store\Resolver
 */
class ResolverTest extends TestCase
{
    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->storeManagerMock = $this->createMock(
            StoreManagerInterface::class
        );

        $this->resolver = $objectManager->getObject(
            Resolver::class,
            [
                'storeManager' => $this->storeManagerMock
            ]
        );
    }

    /**
     * Test getStoreIdForCurrentLabels method
     */
    public function testGetStoreIdForCurrentLabelsStoreIdIsSet()
    {
        $storeId = 2;

        $this->storeManagerMock->expects($this->never())
            ->method('getStore');

        $this->assertEquals($storeId, $this->resolver->getStoreIdForCurrentLabels($storeId));
    }

    /**
     * Test getStoreIdForCurrentLabels method
     */
    public function testGetStoreIdForCurrentLabelsCurrentStoreId()
    {
        $storeId = null;
        $currentStoreId = 12;

        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($currentStoreId);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->assertEquals($currentStoreId, $this->resolver->getStoreIdForCurrentLabels($storeId));
    }

    /**
     * Test getStoreIdForCurrentLabels method
     */
    public function testGetStoreIdForCurrentLabelsCurrentStoreException()
    {
        $storeId = null;
        $currentStoreId = null;

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willThrowException(new NoSuchEntityException());

        $this->assertEquals($currentStoreId, $this->resolver->getStoreIdForCurrentLabels($storeId));
    }
}
