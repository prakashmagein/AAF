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
namespace Aheadworks\RewardPoints\Test\Unit\Model\ResourceModel\EarnRate;

use Aheadworks\RewardPoints\Model\ResourceModel\EarnRate\Collection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class Aheadworks\RewardPoints\Test\Unit\Model\ResourceModel\EarnRate\CollectionTest
 */
class CollectionTest extends TestCase
{
    /**
     * @var Collection
     */
    private $object;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $resource = $this->getMockBuilder(AbstractDb::class)
            ->disableOriginalConstructor()
            ->getMock();

        $connectionMock = $this->getMockBuilder(AdapterInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $selectMock = $this->getMockBuilder(Select::class)
            ->disableOriginalConstructor()
            ->getMock();

        $connectionMock->expects($this->atLeastOnce())
            ->method('select')
            ->willReturn($selectMock);
        $resource->expects($this->once())
            ->method('getConnection')
            ->willReturn($connectionMock);

        $data = [
            'resource' => $resource,
        ];

        $this->object = $objectManager->getObject(Collection::class, $data);
    }

    /**
     * Test toConfigDataArray method
     */
    public function testToConfigDataArray()
    {
        $expectedValues = [
            [
                'website_id' => 5,
                'customer_group_id' => 11,
                'points' => 1000,
                'orig_data' => null,
            ],
            [
                'website_id' => 1,
                'customer_group_id' => 10,
                'points' => 500,
                'orig_data' => null,
            ]

        ];

        foreach ($expectedValues as $value) {
            $this->object->addItem(new \Magento\Framework\DataObject($value));
        }

        $this->assertEquals($expectedValues, $this->object->toConfigDataArray());
    }
}
