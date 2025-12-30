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
namespace Aheadworks\RewardPoints\Test\Unit\Plugin\Model\Newsletter;

use Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface;
use Aheadworks\RewardPoints\Plugin\Model\Newsletter\SubscriberPlugin;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Newsletter\Model\Subscriber;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class Aheadworks\RewardPoints\Test\Unit\Plugin\Model\Newsletter\SubscriberPluginTest
 */
class SubscriberPluginTest extends TestCase
{
    /**
     * @var SubscriberPlugin
     */
    private $object;

    /**
     * @var CustomerRewardPointsManagementInterface|MockObject
     */
    private $customerRewardPointsManagementMock;

    /**
     * @var Subscriber|MockObject
     */
    private $subscriberMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->subscriberMock = $this->getMockBuilder(Subscriber::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getCustomerId',
                    'isSubscribed',
                ]
            )
            ->getMockForAbstractClass();

        $this->customerRewardPointsManagementMock = $this->getMockBuilder(
            CustomerRewardPointsManagementInterface::class
        )
            ->disableOriginalConstructor()
            ->setMethods(['addPointsForNewsletterSignup'])
            ->getMockForAbstractClass();

        $data = [
            'customerRewardPointsService' => $this->customerRewardPointsManagementMock,
        ];

        $this->object = $objectManager->getObject(SubscriberPlugin::class, $data);
    }

    /**
     * Test afterSave method
     */
    public function testAfterSaveMethod()
    {
        $customerId = 3;

        $this->customerRewardPointsManagementMock->expects($this->once())
            ->method('addPointsForNewsletterSignup')
            ->with($customerId)
            ->willReturn(true);

        $this->subscriberMock->expects($this->exactly(1))
            ->method('getCustomerId')
            ->willReturn($customerId);
        $this->subscriberMock->expects($this->once())
            ->method('isSubscribed')
            ->willReturn(true);

        $this->object->afterSave($this->subscriberMock);
    }

    /**
     * Test afterSave method for null customer id
     */
    public function testAfterRegisterMethodNullCustomer()
    {
        $customerId = null;

        $this->customerRewardPointsManagementMock->expects($this->never())
            ->method('addPointsForNewsletterSignup')
            ->willReturnSelf();

        $this->subscriberMock->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $this->object->afterSave($this->subscriberMock);
    }
}
