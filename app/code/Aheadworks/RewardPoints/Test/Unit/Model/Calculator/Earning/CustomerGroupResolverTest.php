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
namespace Aheadworks\RewardPoints\Test\Unit\Model\Calculator\Earning;

use Aheadworks\RewardPoints\Model\Calculator\Earning\CustomerGroupResolver;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\RewardPoints\Model\Calculator\Earning\CustomerGroupResolver
 */
class CustomerGroupResolverTest extends TestCase
{
    /**
     * @var CustomerGroupResolver
     */
    private $resolver;

    /**
     * @var GroupManagementInterface|MockObject
     */
    private $groupManagementMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->groupManagementMock = $this->createMock(GroupManagementInterface::class);

        $this->resolver = $objectManager->getObject(
            CustomerGroupResolver::class,
            [
                'groupManagement' => $this->groupManagementMock,
            ]
        );
    }

    /**
     * Test getCustomerGroupIds method
     *
     * @param GroupInterface|MockObject $groups
     * @param int[] $result
     * @throws LocalizedException
     * @dataProvider getCustomerGroupIdsDataProvider
     */
    public function testGetCustomerGroupIds($groups, $result)
    {
        $this->groupManagementMock->expects($this->once())
            ->method('getLoggedInGroups')
            ->willReturn($groups);

        $this->assertEquals($result, $this->resolver->getCustomerGroupIds());
    }

    /**
     * @return array
     */
    public function getCustomerGroupIdsDataProvider()
    {
        return [
            [
                'groups' =>  [
                    $this->getCustomerGroupMock(10),
                    $this->getCustomerGroupMock(11)
                ],
                'result' => [10, 11]
            ],
            [
                'groups' =>  [
                    $this->getCustomerGroupMock(10)
                ],
                'result' => [10]
            ],
            [
                'groups' =>  [],
                'result' => []
            ],
        ];
    }

    /**
     * Test getAllCustomerGroupId method
     */
    public function testGetAllCustomerGroupId()
    {
        $allCustomerGroupId = 32000;
        $groupMock = $this->getCustomerGroupMock($allCustomerGroupId);

        $this->groupManagementMock->expects($this->once())
            ->method('getAllCustomersGroup')
            ->willReturn($groupMock);

        $this->assertEquals($allCustomerGroupId, $this->resolver->getAllCustomerGroupId());
    }

    /**
     * Test getAllCustomerGroupId method if an error occurs
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Error!
     */
    public function testGetAllCustomerGroupIdError()
    {
        $this->groupManagementMock->expects($this->once())
            ->method('getAllCustomersGroup')
            ->willThrowException(new LocalizedException(__('Error!')));
$this->expectException(LocalizedException::class);
        $this->resolver->getAllCustomerGroupId();
    }

    /**
     * Get customer group mock
     *
     * @param int $customerGroupId
     * @return GroupInterface|MockObject
     */
    private function getCustomerGroupMock($customerGroupId)
    {
        $groupMock = $this->createMock(GroupInterface::class);
        $groupMock->expects($this->once())
            ->method('getId')
            ->willReturn($customerGroupId);

        return $groupMock;
    }
}
