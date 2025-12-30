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
namespace Aheadworks\RewardPoints\Test\Unit\Plugin\Model\Product;

use Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface;
use Aheadworks\RewardPoints\Model\ResourceModel\Order as OrderResource;
use Aheadworks\RewardPoints\Plugin\Model\Product\ReviewPlugin;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Review\Model\Review;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class Aheadworks\RewardPoints\Test\Unit\Plugin\Model\Service\OrderServicePluginTest
 */
class ReviewPluginTest extends TestCase
{
    /**
     * @var ReviewPlugin
     */
    private $object;

    /**
     * @var CustomerRewardPointsManagementInterface|MockObject
     */
    private $customerRewardPointsManagementMock;

    /**
     * @var Review|MockObject
     */
    private $reviewMock;

    /**
     * @var Order|MockObject
     */
    private $orderResourceMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->orderResourceMock = $this->getMockBuilder(OrderResource::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->reviewMock = $this->getMockBuilder(Review::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getCustomerId',
                    'isApproved',
                    'getOrigData',
                    'getStatusId',
                    'getEntityPkValue',
                    'dataHasChangedFor'
                ]
            )
            ->getMockForAbstractClass();

        $this->customerRewardPointsManagementMock = $this->getMockBuilder(
            CustomerRewardPointsManagementInterface::class
        )
            ->disableOriginalConstructor()
            ->setMethods(['addPointsForReviews'])
            ->getMockForAbstractClass();

        $data = [
            'customerRewardPointsService' => $this->customerRewardPointsManagementMock,
            'orderResource' => $this->orderResourceMock
        ];

        $this->object = $objectManager->getObject(ReviewPlugin::class, $data);
    }

    /**
     * Test beforeSave method for customer review after approving
     */
    public function testBeforeSaveAfterApproveMethod()
    {
        $customerId = 2;
        $isApproved = 1;

        $this->reviewMock->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $this->reviewMock->expects($this->once())
            ->method('dataHasChangedFor')
            ->with('status_id')
            ->willReturn(true);

        $this->reviewMock->expects($this->once())
            ->method('isApproved')
            ->willReturn($isApproved);

        $this->object->beforeSave($this->reviewMock);
    }

    /**
     * Test beforeSave method for guest review after approving
     */
    public function testBeforeSaveGuestReviewMethod()
    {
        $customerId = 0;
        $isApproved = 1;

        $this->reviewMock->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $this->reviewMock->expects($this->never())
            ->method('dataHasChangedFor')
            ->with('status_id')
            ->willReturn(true);

        $this->reviewMock->expects($this->never())
            ->method('isApproved')
            ->willReturn($isApproved);

        $this->object->beforeSave($this->reviewMock);
    }

    /**
     * Test beforeSave method for customer review after editing
     */
    public function testBeforeSaveEditReviewMethod()
    {
        $customerId = 2;
        $isApproved = 1;

        $this->reviewMock->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $this->reviewMock->expects($this->once())
            ->method('dataHasChangedFor')
            ->with('status_id')
            ->willReturn(false);

        $this->reviewMock->expects($this->never())
            ->method('isApproved')
            ->willReturn($isApproved);

        $this->object->beforeSave($this->reviewMock);
    }

    /**
     * Test afterSave method for customer review after editing
     */
    public function testAfterSaveNotApproveReviewMethod()
    {
        $customerId = 2;

        $this->reviewMock->expects($this->never())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $this->customerRewardPointsManagementMock->expects($this->never())
            ->method('addPointsForReviews')
            ->with($customerId)
            ->willReturnSelf();

        $this->object->afterSave($this->reviewMock);
    }

    /**
     * Test afterSave method for customer review after approving
     */
    public function testAfterSaveApproveReviewMethod()
    {
        $customerId = 2;
        $productId = 1049;

        $property = new \ReflectionProperty(ReviewPlugin::class, "isApprovedReview");
        $property->setAccessible(true);
        $property->setValue($this->object, true);

        $this->reviewMock->expects($this->exactly(1))
            ->method('getCustomerId')
            ->willReturn($customerId);

        $this->reviewMock->expects($this->exactly(1))
            ->method('getEntityPkValue')
            ->willReturn($productId);

        $this->orderResourceMock->expects($this->once())
            ->method('isCustomersOwnerOfProductId')
            ->with($customerId, $productId)
            ->willReturnSelf();

        $this->customerRewardPointsManagementMock->expects($this->once())
            ->method('addPointsForReviews')
            ->with($customerId)
            ->willReturnSelf();

        $this->object->afterSave($this->reviewMock);
    }
}
