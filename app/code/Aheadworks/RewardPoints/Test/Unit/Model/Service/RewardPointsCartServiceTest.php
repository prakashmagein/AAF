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
namespace Aheadworks\RewardPoints\Test\Unit\Model\Service;

use Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface;
use Aheadworks\RewardPoints\Model\Service\RewardPointsCartService;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Store\Api\Data\StoreInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class Aheadworks\RewardPoints\Test\Unit\Model\Service$RewardPointsCartServiceTest
 */
class RewardPointsCartServiceTest extends TestCase
{
    /**
     * @var RewardPointsCartService
     */
    private $object;

    /**
     * @var CustomerRewardPointsManagementInterface|MockObject
     */
    private $customerRewardPointsManagementMock;

    /**
     * @var CartRepositoryInterface|MockObject
     */
    private $quoteRepositoryMock;

    /**
     * @var CartInterface|MockObject
     */
    private $quoteMock;

    /**
     * @var StoreInterface|MockObject
     */
    private $storeMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->customerRewardPointsManagementMock = $this->getMockBuilder(
            CustomerRewardPointsManagementInterface::class
        )
            ->disableOriginalConstructor()
            ->setMethods(['getCustomerRewardPointsBalance'])
            ->getMockForAbstractClass();

        $this->quoteRepositoryMock = $this->getMockBuilder(CartRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getActive', 'save'])
            ->getMockForAbstractClass();

        $this->quoteMock = $this->getMockBuilder(CartInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getAwUseRewardPoints',
                    'setAwUseRewardPoints',
                    'setAwRewardPointsQtyToApply',
                    'getItemsCount',
                    'getCustomerId',
                    'getShippingAddress',
                    'collectTotals',
                    'getStore'
                ]
            )->getMockForAbstractClass();

        $data = [
            'customerRewardPointsService' => $this->customerRewardPointsManagementMock,
            'quoteRepository' => $this->quoteRepositoryMock
        ];

        $this->object = $objectManager->getObject(RewardPointsCartService::class, $data);
    }

    /**
     * Test get method
     */
    public function testGetMethod()
    {
        $cartId = 5;
        $awUseRewardPoints = true;

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($cartId)
            ->willReturn($this->quoteMock);

        $this->quoteMock->expects($this->once())
            ->method('getItemsCount')
            ->willReturn(1);

        $this->quoteMock->expects($this->once())
            ->method('getAwUseRewardPoints')
            ->willReturn($awUseRewardPoints);

        $this->assertTrue($this->object->get($cartId));
    }

    /**
     * Test get method, throw exception
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage Cart 5 doesn't contain products
     */
    public function testGetMethodException()
    {
        $cartId = 5;
        $awUseRewardPoints = true;

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($cartId)
            ->willReturn($this->quoteMock);

        $this->quoteMock->expects($this->once())
            ->method('getItemsCount')
            ->willReturn(0);

        $this->quoteMock->expects($this->never())
            ->method('getAwUseRewardPoints')
            ->willReturn($awUseRewardPoints);
        $this->expectException(NoSuchEntityException::class);
        $this->object->get($cartId);
    }

    /**
     * Test set method
     */
    public function testSetMethod()
    {
        $cartId = 10;
        $customerId = 4;
        $pointsQty = 5;

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($cartId)
            ->willReturn($this->quoteMock);

        $this->quoteMock->expects($this->once())
            ->method('getItemsCount')
            ->willReturn(2);
        $this->quoteMock->expects($this->exactly(3))
            ->method('getCustomerId')
            ->willReturn($customerId);

        $this->customerRewardPointsManagementMock->expects($this->once())
            ->method('getCustomerRewardPointsBalance')
            ->with($customerId)
            ->willReturn(12);

        $shippingAddressMock = $this->getMockBuilder(AddressInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['setCollectShippingRates'])
            ->getMockForAbstractClass();
        $shippingAddressMock->expects($this->once())
            ->method('setCollectShippingRates')
            ->with(true)
            ->willReturnSelf();

        $this->quoteMock->expects($this->once())
            ->method('getShippingAddress')
            ->willReturn($shippingAddressMock);
        $this->quoteMock->expects($this->once())
            ->method('setAwUseRewardPoints')
            ->with(true)
            ->willReturnSelf();
        $this->quoteMock->expects($this->once())
            ->method('setAwRewardPointsQtyToApply')
            ->with($pointsQty)
            ->willReturnSelf();
        $this->quoteMock->expects($this->once())
            ->method('collectTotals')
            ->willReturnSelf();
        $storeMock = $this->getMockBuilder(StoreInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getWebsiteId'])
            ->getMockForAbstractClass();
        $storeMock->expects($this->any())
            ->method('getWebsiteId')
            ->willReturn(1);
        $this->quoteMock->expects($this->any())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->quoteRepositoryMock->expects($this->once())
            ->method('save')
            ->with($this->quoteMock)
            ->willReturnSelf();
        $this->quoteMock->expects($this->once())
            ->method('getAwUseRewardPoints')
            ->willReturn(true);
        $this->assertTrue(is_array($this->object->set($cartId, $pointsQty)));
    }

    /**
     * Test set method if quote not has items
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage Cart 10 doesn't contain products
     */
    public function testSetMethodNotQuoteItems()
    {
        $cartId = 10;
        $pointsQty = 5;

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($cartId)
            ->willReturn($this->quoteMock);

        $this->quoteMock->expects($this->once())
            ->method('getItemsCount')
            ->willReturn(0);
        $this->expectException(NoSuchEntityException::class);
        $this->object->set($cartId, $pointsQty);
    }

    /**
     * Test set method if customer id is null
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No reward points to be used
     */
    public function testSetMethodNullCustomerId()
    {
        $cartId = 10;
        $pointsQty = 5;

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($cartId)
            ->willReturn($this->quoteMock);

        $this->quoteMock->expects($this->once())
            ->method('getItemsCount')
            ->willReturn(1);
        $this->quoteMock->expects($this->exactly(2))
            ->method('getCustomerId')
            ->willReturn(null);

        $storeMock = $this->getMockBuilder(StoreInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getWebsiteId'])
            ->getMockForAbstractClass();
        $storeMock->expects($this->any())
            ->method('getWebsiteId')
            ->willReturn(1);
        $this->quoteMock->expects($this->any())
            ->method('getStore')
            ->willReturn($storeMock);
        $this->expectException(NoSuchEntityException::class);
        $this->object->set($cartId, $pointsQty);
    }

    /**
     * Test set method if customer has null reward points balance
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No reward points to be used
     */
    public function testSetMethodNullCustomerRewardPointsBalance()
    {
        $cartId = 10;
        $customerId = 5;
        $pointsQty = 5;

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($cartId)
            ->willReturn($this->quoteMock);

        $this->quoteMock->expects($this->once())
            ->method('getItemsCount')
            ->willReturn(1);
        $this->quoteMock->expects($this->exactly(3))
            ->method('getCustomerId')
            ->willReturn($customerId);

        $storeMock = $this->getMockBuilder(StoreInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getWebsiteId'])
            ->getMockForAbstractClass();
        $storeMock->expects($this->any())
            ->method('getWebsiteId')
            ->willReturn(1);
        $this->quoteMock->expects($this->any())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->customerRewardPointsManagementMock->expects($this->once())
            ->method('getCustomerRewardPointsBalance')
            ->with($customerId)
            ->willReturn(0);
        $this->expectException(NoSuchEntityException::class);
        $this->object->set($cartId, $pointsQty);
    }

    /**
     * Test set method throw exception at save repository
     *
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage Could not apply reward points
     */
    public function testSetMethodThrowExceptionAtSaveRepository()
    {
        $cartId = 10;
        $customerId = 4;
        $pointsQty = 5;

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($cartId)
            ->willReturn($this->quoteMock);

        $this->quoteMock->expects($this->once())
            ->method('getItemsCount')
            ->willReturn(2);
        $this->quoteMock->expects($this->exactly(3))
            ->method('getCustomerId')
            ->willReturn($customerId);

        $this->customerRewardPointsManagementMock->expects($this->once())
            ->method('getCustomerRewardPointsBalance')
            ->with($customerId)
            ->willReturn(12);

        $shippingAddressMock = $this->getMockBuilder(AddressInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['setCollectShippingRates'])
            ->getMockForAbstractClass();
        $shippingAddressMock->expects($this->once())
            ->method('setCollectShippingRates')
            ->with(true)
            ->willReturnSelf();

        $this->quoteMock->expects($this->once())
            ->method('getShippingAddress')
            ->willReturn($shippingAddressMock);
        $this->quoteMock->expects($this->once())
            ->method('setAwUseRewardPoints')
            ->with(true)
            ->willReturnSelf();
        $this->quoteMock->expects($this->once())
            ->method('setAwRewardPointsQtyToApply')
            ->with($pointsQty)
            ->willReturnSelf();
        $this->quoteMock->expects($this->once())
            ->method('collectTotals')
            ->willReturnSelf();

        $storeMock = $this->getMockBuilder(StoreInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getWebsiteId'])
            ->getMockForAbstractClass();
        $storeMock->expects($this->any())
            ->method('getWebsiteId')
            ->willReturn(1);
        $this->quoteMock->expects($this->any())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->quoteRepositoryMock->expects($this->once())
            ->method('save')
            ->with($this->quoteMock)
            ->willThrowException(new \Exception('Oh oh oh!!!'));
        $this->expectException(\Exception::class);
        $this->object->set($cartId, $pointsQty);
    }

    /**
     * Test remove method
     */
    public function testRemoveMethod()
    {
        $cartId = 9;

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($cartId)
            ->willReturn($this->quoteMock);

        $this->quoteMock->expects($this->once())
            ->method('getItemsCount')
            ->willReturn(2);

        $shippingAddressMock = $this->getMockBuilder(AddressInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['setCollectShippingRates'])
            ->getMockForAbstractClass();
        $shippingAddressMock->expects($this->once())
            ->method('setCollectShippingRates')
            ->with(true)
            ->willReturnSelf();

        $this->quoteMock->expects($this->once())
            ->method('getShippingAddress')
            ->willReturn($shippingAddressMock);
        $this->quoteMock->expects($this->once())
            ->method('setAwUseRewardPoints')
            ->with(false)
            ->willReturnSelf();
        $this->quoteMock->expects($this->once())
            ->method('collectTotals')
            ->willReturnSelf();

        $this->quoteRepositoryMock->expects($this->once())
            ->method('save')
            ->with($this->quoteMock)
            ->willReturnSelf();

        $this->assertTrue($this->object->remove($cartId));
    }

    /**
     * Test remove method if quote not has items
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage Cart 21 doesn't contain products
     */
    public function testRemoveMethodNotQuoteItems()
    {
        $cartId = 21;

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($cartId)
            ->willReturn($this->quoteMock);

        $this->quoteMock->expects($this->once())
            ->method('getItemsCount')
            ->willReturn(0);
        $this->expectException(NoSuchEntityException::class);
        $this->object->remove($cartId);
    }

    /**
     * Test remove method throw exception at save repository
     *
     * @expectedException \Magento\Framework\Exception\CouldNotDeleteException
     * @expectedExceptionMessage Could not remove reward points
     */
    public function testRemoveMethodThrowExceptionAtSaveRepository()
    {
        $cartId = 12;

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($cartId)
            ->willReturn($this->quoteMock);

        $this->quoteMock->expects($this->once())
            ->method('getItemsCount')
            ->willReturn(2);

        $shippingAddressMock = $this->getMockBuilder(AddressInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['setCollectShippingRates'])
            ->getMockForAbstractClass();
        $shippingAddressMock->expects($this->once())
            ->method('setCollectShippingRates')
            ->with(true)
            ->willReturnSelf();

        $this->quoteMock->expects($this->once())
            ->method('getShippingAddress')
            ->willReturn($shippingAddressMock);
        $storeMock = $this->getMockBuilder(StoreInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMockForAbstractClass();
        $this->quoteMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);
        $storeMock->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn(1);
        $this->quoteMock->expects($this->once())
            ->method('setAwUseRewardPoints')
            ->with(false)
            ->willReturnSelf();
        $this->quoteMock->expects($this->once())
            ->method('collectTotals')
            ->willReturnSelf();


        $this->quoteRepositoryMock->expects($this->once())
            ->method('save')
            ->with($this->quoteMock)
            ->willThrowException(new \Exception('Oh oh oh!!!'));
        $this->expectException(CouldNotDeleteException::class);
        $this->object->remove($cartId);
    }
}
