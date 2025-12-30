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
namespace Aheadworks\RewardPoints\Test\Unit\Plugin\Model\Sales;

use Aheadworks\RewardPoints\Api\CustomerRewardPointsManagementInterface;
use Aheadworks\RewardPoints\Plugin\Model\Sales\InvoicePlugin;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Sales\Model\Order\Invoice;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class Aheadworks\RewardPoints\Test\Unit\Plugin\Model\Service\InvoicePluginTest
 */
class InvoicePluginTest extends TestCase
{
    /**
     * @var InvoicePlugin
     */
    private $object;

    /**
     * @var CustomerRewardPointsManagementInterface|MockObject
     */
    private $customerRewardPointsManagementMock;

    /**
     * @var Invoice|MockObject
     */
    private $invoiceMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->invoiceMock = $this->getMockBuilder(Invoice::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getEntityId'
                ]
            )->getMock();

        $this->customerRewardPointsManagementMock = $this->getMockBuilder(
            CustomerRewardPointsManagementInterface::class
        )
            ->disableOriginalConstructor()
            ->setMethods(['addPointsForPurchases'])
            ->getMockForAbstractClass();

        $data = [
            'customerRewardPointsService' => $this->customerRewardPointsManagementMock
        ];

        $this->object = $objectManager->getObject(InvoicePlugin::class, $data);
    }

    /**
     * Test afterSave method
     */
    public function testAfterSaveMethod()
    {
        $entityId = 1;

        $this->invoiceMock->expects($this->once())
            ->method('getEntityId')
            ->willReturn($entityId);
        $this->customerRewardPointsManagementMock->expects($this->once())
            ->method('addPointsForPurchases')
            ->with($entityId)
            ->willReturnSelf();

        $invoiceMock = $this->getMockBuilder(Invoice::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->object->afterPay($invoiceMock);

        $this->object->afterSave($this->invoiceMock);
    }
}
